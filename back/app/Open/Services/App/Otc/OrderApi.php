<?php
namespace App\Open\Services\App\Otc;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\FreezeLog;
use App\Http\Models\Otc;
use App\Http\Models\OtcOrder;
use App\Http\Models\Payment;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\DB;


class OrderApi extends FanweAuthService
{

    /**
     * @name index
     * @description 获取用户的订单记录
     * @param
     * type: 订单类型（1：买入；2：卖出），不传参默认获取买入的
     * order_id: （订单ID）传参则获取某一条交易订单的详情
     * page：页数
     * @return order_list
     * [
     * "id" => 订单ID,
     * "seller" => 卖家ID,
     * "buyer" => 买家ID,
     * "vc_amount" => 交易数量,
     * "vc_uint_price" => 交易单价,
     * "vc_total_price" => 交易总额,
     * "otc_id" => 挂单ID,
     * "create_time" => 下单时间,
     * "pay_time" => 支付时间,
     * "send_time" => 发币时间,
     * "status" => 订单状态 0：未付款；1：已付款未发币；2：已发币；4：取消,
     * "appeal_status" => 申诉状态 0：未申诉；1：买家申诉(没收到币);2：卖家申诉（没收到钱）,
     * "cancel_time" => 取消订单时间,
     * "buyersellername" => 买入时显示卖家名称，卖出时显示买家名称，
     * "identity" => 身份（buyer：买家；seller：卖家）,
     * "order_sn" => 订单编号
     * ]
     */
    public function index($param)
    {
        $user = $this->user;
        $order_id = $param["order_id"];
        $type = $param["type"]?$param["type"]:1;
        if($type==1)
        {
            $action = "purchase";
        }
        elseif($type==2)
        {
            $action = "sale";
        }
        else
        {
            return $this->error("订单类型错误",FanweErrcode::ORDER_TYPE_ERROR);
        }
        $order_list = OtcOrder::getOtcOrderList($user->id,$action,$param);
        //格式化数据
        $order_list = $order_list->toArray()["data"];
        $order_data = [];
        foreach($order_list as $item)
        {
            $item["order_sn"] = Helper::formatOrderSN($item["create_time"],$item["id"]);
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["create_time"] = date("Y-m-d H:i:s",$item["create_time"]);
            $item["pay_time"] = date("Y-m-d H:i:s",$item["pay_time"]);
            $item["send_time"] = date("Y-m-d H:i:s",$item["send_time"]);
            $item["cancel_time"] = date("Y-m-d H:i:s",$item["cancel_time"]);
            $item["create_time_stamp"] = strtotime($item["create_time"]);
            $item["pay_time_stamp"] = strtotime($item["pay_time"]);
            if($action=="sale")
            {
                //出售
                $item["identity"] = "seller";
            }
            else
            {
                //购买
                $item["identity"] = "buyer";
            }

            //卖家发币是否超时
            $diff_sec = time() -  strtotime($item["pay_time"]);
            $item["seller_time_over"] = false;
            if($diff_sec>600){
                $item["seller_time_over"] = true;
            }
            array_push($order_data,$item);
        }
        //查看订单详情时，获取支付方式
        if($order_id>0){
            $payment_info = Payment::where("user_id",$order_data[0]["seller"])->get()->toArray();
            if($payment_info)
            {
                $payment_data = [];
                foreach ($payment_info as $item)
                {
                    if($item["payment_key"]!="alipay"&&$item["payment_key"]!="weixin")
                    {
                        $payment_data["bankcard"] = $item;
                    }
                    else
                    {
                        $payment_data[$item["payment_key"]] = $item;
                    }
                }
                $order_data[0]["payment"] = $payment_data;
            }
        }
        $this->setData("order_list",$order_data);
        return $this->success();
    }

    /**
     * @name appeal
     * @description 提交申诉
     * @param
     * order_id：订单ID
     * type: 订单类型（1：买入；2：卖出）
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function appeal($param)
    {
        $user = $this->user;
        $order_id = $param["order_id"]; //订单ID
        $type = $param["type"]; //订单类型
        if(!$order_id)
        {
            return $this->error("请输入订单ID",FanweErrcode::OTC_ORDER_ID_NOT_EXSITS);
        }
        if(!$type)
        {
            return $this->error("请输入订单类型",FanweErrcode::ORDER_TYPE_NOT_EXSITS);
        }
        if($type==1)
        {
            //买家
            $otc_order = OtcOrder::where(["id"=>$order_id,"buyer"=>$user->id])->first();
        }
        elseif($type==2)
        {
            //卖家
            $otc_order = OtcOrder::where(["id"=>$order_id,"seller"=>$user->id])->first();
        }
        else
        {
            return $this->error("订单类型错误",FanweErrcode::ORDER_TYPE_ERROR);
        }
        if(!$otc_order)
        {
            return $this->error("订单不存在",FanweErrcode::OTC_ORDER_NOT_EXSITS);
        }
        if($otc_order->status!=1)
        {
            return $this->error('只有已付款未发币可以申诉');
        }
        DB::beginTransaction();
        try{
            $appeal_status = 1;
            if($type==2)$appeal_status = 2;
            //对方有申诉，状态改成4，双方都申诉
            if($otc_order->appeal_status==1||$otc_order->appeal_status==2)$appeal_status = 4;
            $otc_order->appeal_status = $appeal_status;
            $otc_order->save();
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name cancel
     * @description 取消订单
     * @param
     * order_id：订单ID
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function cancel($param)
    {
        $user = $this->user;
        $order_id = $param["order_id"]; //订单ID
        if(!$order_id)
        {
            return $this->error("请输入订单ID",FanweErrcode::OTC_ORDER_ID_NOT_EXSITS);
        }
        //取消订单，只针对买家操作
        $otc_order = OtcOrder::where(["id"=>$order_id,"buyer"=>$user->id,"status"=>0])->first();
        if(!$otc_order)
        {
            return $this->error("订单不存在",FanweErrcode::OTC_ORDER_NOT_EXSITS);
        }
        DB::beginTransaction();
        try{
            $otc_order->status = 3;
            $otc_order->cancel_time = time();
            $otc_order->save();
            //解锁金额
            $otc = Otc::where(["id"=>$otc_order->otc_id])->first();
            $freeze_id = FreezeLog::where(["relate"=>$otc->id,"type"=>"sale"])->first()->id;
            Helper::unlockCoin($freeze_id,$otc_order->vc_amount + $otc_order->vc_fee);
            //返还otc挂卖交易单的剩余数量
            $otc->vc_less_amount += $otc_order->vc_amount;
            $otc->vc_less_fee += $otc_order->vc_fee;
            $otc->save();
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

}