<?php
namespace App\Open\Services\App\Otc;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\FreezeLog;
use App\Http\Models\Otc;
use App\Http\Models\OtcOrder;
use App\Http\Models\Payment;
use App\Http\Models\PriceTrend;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\DB;



class DealsApi extends FanweAuthService
{

    /**
     * @name index
     * @description 获取交易挂单列表
     * @param
     * type：OTC单类型（0：挂卖；1：挂买），不传参默认获取挂卖的
     * otc_id: （挂单ID）传参则获取某一条挂单的详情
     * myself: true（是否显示自己的挂单记录，不传参显示所有人）
     * page：页数
     * orderkey：排序（vc_amount：数量；vc_unit_price：单价），不传参按照时间
     * orderby：desc倒序（默认） asc正序
     * @return otc_list
     * [
     * "id" => 挂单ID,
     * "user_id" => 用户ID,
     * "type" => OTC单类型,
     * "vc_amount" => 总挂单量,
     * "vc_less_amount" => 剩余的量,
     * "vc_unit_price" => 单价,
     * "create_time" => 上架时间,
     * "down_time" => 下架时间,
     * "username" => 挂单用户名称,
     * ]
     */
    public function index($param)
    {
        $otc_id = $param["otc_id"];
        $myself = $param["myself"];
        if($myself)
        {
            $param["user_id"] = $this->user->id;
        }
        $otc_list = Otc::getOtcList($param);
        //格式化数据
        $otc_list = $otc_list->toArray()["data"];
        if(!$otc_list&&$otc_id>0)
        {
            //查看挂单详情
            return $this->error("已下架或已售罄",FanweErrcode::OTC_NOT_EXSITS);
        }
        $otc_data = [];
        foreach($otc_list as $item)
        {
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["vc_less_amount"] = Helper::formatCoin($item["vc_less_amount"],1);
            $item["total_price"] = Helper::formatRMB($item["vc_less_amount"]*$item["vc_unit_price"]);
            array_push($otc_data,$item);
        }
        //查看挂单详情时，获取支付方式
        if($otc_id>0){
            $payment_info = Payment::where("user_id",$otc_data[0]["user_id"])->get()->toArray();
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
                $otc_data[0]["payment"] = $payment_data;
            }
        }
        $this->setData("otc_list",$otc_data);
        return $this->success();
    }

    /**
     * @name push
     * @description 交易挂卖
     * @param
     * amount：出售的数量
     * price: 出售的单价
     * lock_day 锁仓天数
     * is_lock 是否锁仓
     * @return
     * 成功：返回otc_id挂单ID
     * 失败：返回错误码及提示
     */
    public function push($param)
    {
        $user = $this->user;
        $vc_normal = $user->vc_normal; //可交易的余额
        $amount = $param["amount"]; //出售的数量
        $price = $param["price"]; //出售的单价
        $fee_rate = db_config('OTC_FEE_RATE');//手续费比率
        $lock_day = $param['lock_day'];
        $is_lock = $param['is_lock'];
        if($amount<=0)
        {
            return $this->error("请输入要出售的数量",FanweErrcode::OTC_AMOUNT_NOT_EXSITS);
        }
        if($amount<db_config("MIN_OTC_SALE"))
        {
            return $this->error("出售数量不得低于".db_config("MIN_OTC_SALE"),FanweErrcode::OTC_SALE_MIN_LESS);
        }
        if(!$price)
        {
            return $this->error("请输入出售的单价",FanweErrcode::OTC_PRICE_NOT_EXSITS);
        }
        if($amount*(1+$fee_rate)>floatval($vc_normal))
        {
            return $this->error("要出售的数量累计手续费超出可交易的数量",FanweErrcode::OTC_SALE_AMOUNT_OVER);
        }
        if($user->violate_date == date('Y-m-d') && $user->violate_count >=db_config('DAY_VIOLATE_LIMIT')){
            return $this->error('今日违约次数太多，不允许出售',FanweErrcode::USER_MAX_BREAK);
        }
        $otc_sale_limit= intval(db_config('OTC_SALE_LIMIT'));
        $count = Otc::where(['user_id'=>$user->id,'status'=>1])->where("vc_less_amount",">",0)->count();
        if($count>=$otc_sale_limit){
            return $this->error('超出最大同时挂单数量',FanweErrcode::USER_MAX_SELL);
        }
        $payment = Payment::where("user_id",$user->id)->first();
        if(!$payment)
        {
            return $this->error("您还未绑定支付方式",FanweErrcode::USER_PAYTYPE_NOT_EXSITS);
        }
        if(db_config('OTC_SALE_AUTH')&&!$user->otc_auth)
        {
            return $this->error("没有挂单出售权限");
        }
        if($is_lock)
        {
            if(!$lock_day)
            {
                return $this->error('请输入正确的锁仓天数');
            }
            if($user->otc_auth_type == 0)
            {
                return $this->error('没有锁仓权限');
            }
            elseif($user->otc_auth_type==1)
            {
                if($lock_day<$user->limit_day)
                {
                    return $this->error("最低锁仓{$user->limit_day}天");
                }
            }elseif ($user->otc_auth_type==2)
            {
                $lock_day = $user->limit_day;
            }
        }
        DB::beginTransaction();
        try{
            $otc = new Otc();
            $otc->user_id = $user->id;
            $otc->type = 0;
            $otc->vc_amount = $amount;
            $otc->vc_less_amount = $amount;
            $otc->vc_unit_price = $price;
            $otc->create_time = date("Y-m-d H:i:s",time());
            $otc->status = 1;
            $otc->vc_fee = Helper::trimNumber($fee_rate*$amount,8);
            $otc->vc_less_fee = $otc->vc_fee;
            $otc->vc_fee_rate = $fee_rate;
            $otc->is_lock = $is_lock;
            $otc->lock_day = $lock_day;
            $otc->save();
            Helper::freezeCoin("sale",$otc->id);
            DB::commit();
            $this->setData("otc_id",$otc->id);
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name buy
     * @description 交易购买
     * @param
     * otc_id：交易挂单ID
     * amount: 购买的数量
     * @return
     * 成功：返回order_id订单ID
     * 失败：返回错误码及提示
     */
    public function buy($param)
    {
        $user = $this->user;
        $otc_id = $param["otc_id"]; //交易挂单ID
        $amount = $param["amount"]; //出售的数量
        $payment_info = $param['payment_info'];//支付方式
        if(!$otc_id)
        {
            return $this->error("请输入交易挂单ID",FanweErrcode::OTC_ID_NOT_EXSITS);
        }
        if($amount<=0)
        {
            return $this->error("请输入要购买的数量",FanweErrcode::OTC_AMOUNT_NOT_EXSITS);
        }
        if($user->violate_date == date('Y-m-d') && $user->violate_count >=db_config('DAY_VIOLATE_LIMIT')){
            return $this->error('今日违约次数太多，不允许购买',FanweErrcode::USER_MAX_BREAK);
        }
        $otc_buy_limit = intval(db_config('OTC_BUY_LIMIT'));
        $count = OtcOrder::where('buyer',$user->id)->where('status','<','2')->count();
        if($count>=$otc_buy_limit){
            return $this->error('超出最大同时购买数量',FanweErrcode::USER_MAX_BUY);
        }
//        if($amount<db_config("MIN_OTC_BUY"))
//        {
//            return $this->error("购买数量不得低于".db_config("MIN_OTC_BUY"),FanweErrcode::OTC_BUY_MIN_LESS);
//        }
        $otc_info = Otc::where(["id"=>$otc_id,"status"=>1])->first(); //正常上架
        if(!$otc_info)
        {
            return $this->error("挂卖的交易单不存在",FanweErrcode::OTC_SALE_NOT_EXSITS);
        }
        if($otc_info->user_id==$user->id)
        {
            return $this->error("不允许购买自己所出售的",FanweErrcode::OTC_BUY_NOT_ALLOW_MINE);
        }
        if($amount>$otc_info->vc_less_amount)
        {
            return $this->error("购买的数量超出售卖的剩余数量",FanweErrcode::OTC_BUY_AMOUNT_OVER);
        }
        DB::beginTransaction();
        try{
            $otc_order = new OtcOrder();
            $otc_order->seller = $otc_info->user_id;
            $otc_order->buyer = $user->id;
            $otc_order->vc_amount = $amount;
            $otc_order->vc_uint_price = $otc_info->vc_unit_price;
            $otc_order->vc_total_price = $amount*$otc_info->vc_unit_price;
            $otc_order->otc_id = $otc_info->id;
            $otc_order->create_time = time();
            $otc_order->status = 0;
            $otc_order->payment_info = json_encode($payment_info);
            $otc_order->vc_fee = Helper::trimNumber($otc_order->vc_amount*$otc_info->vc_fee_rate,8);//手续费计算
            if($otc_order->vc_fee > $otc_info->vc_less_fee){
                $otc_order->vc_fee = $otc_info->vc_less_fee;
            }
            $otc_order->lock_day = $otc_info->lock_day;
            $otc_order->is_lock = $otc_info->is_lock;
            $otc_order->lock_end_time = date('Y-m-d H:i:s',strtotime("+{$otc_info->lock_day} day"));
            $otc_order->last_release_time = date('Y-m-d H:i:s');
            $otc_order->free = 0;
            $otc_order->less_amount =$amount;
            $otc_order->remain_time = $otc_order->lock_day;
            $otc_order->save();
            $freeze_id = FreezeLog::where(["relate"=>$otc_info->id,"type"=>"sale"])->first()->id;
            Helper::lockCoin($freeze_id,$amount + $otc_order->vc_fee);
            //扣减otc挂卖交易单的剩余数量
            $otc_info->vc_less_amount -= $amount;
            $otc_info->vc_less_fee -= $otc_order->vc_fee;//扣减剩余手续费
            $otc_info->save();
            DB::commit();
            $this->setData("order_id",$otc_order->id);
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name confirmpay
     * @description 交易确认支付（买家付款）
     * @param
     * order_id：订单ID
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function confirmpay($param)
    {
        $user = $this->user;
        $order_id = $param["order_id"]; //订单ID
        if(!$order_id)
        {
            return $this->error("请输入订单ID",FanweErrcode::OTC_ORDER_ID_NOT_EXSITS);
        }
        $otc_order = OtcOrder::where(["id"=>$order_id,"buyer"=>$user->id,"status"=>0])->first(); //未付款
        if(!$otc_order)
        {
            return $this->error("订单不存在",FanweErrcode::OTC_ORDER_NOT_EXSITS);
        }
        DB::beginTransaction();
        try{
            $otc_order->pay_time = time();
            $otc_order->status = 1;
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
     * @name confirmreceive
     * @description 交易确认收款（卖家放币）
     * @param
     * order_id：订单ID
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function confirmreceive($param)
    {
        $user = $this->user;
        $order_id = $param["order_id"]; //订单ID
        if(!$order_id)
        {
            return $this->error("请输入订单ID",FanweErrcode::OTC_ORDER_ID_NOT_EXSITS);
        }
        $otc_order = OtcOrder::where(["id"=>$order_id,"seller"=>$user->id,"status"=>1])->first(); //已付款
        if(!$otc_order)
        {
            return $this->error("订单不存在",FanweErrcode::OTC_ORDER_NOT_EXSITS);
        }
        DB::beginTransaction();
        try{
            $otc_order->send_time = time();
            $otc_order->status = 2;
         
            $otc_order->save();
            //生成卖家支出
            Helper::expendCoin("sale",$otc_order->id);
            //生成买家收入
            Helper::incomeCoin("purchase",$otc_order->id);
            //锁仓冻结
            if($otc_order->is_lock && $otc_order->lock_day>0)
            {
                $otc_order->freeze_log_id = Helper::freezeCoin('otc_lock',$otc_order->id);
            }
            $otc_order->save();
            //生成最新价格
            $price_trend = new PriceTrend();
            $price_trend->price = $otc_order->vc_uint_price;
            $price_trend->create_time = date("Y-m-d H:i:s");
            $price_trend->save();
            //购买后数量小于最少购买量  下架
            $otc_info = Otc::where('id',$otc_order->otc_id)->first();
            if($otc_info->vc_less_amount<db_config('MIN_OTC_BUY') && $otc_info->vc_less_amount>0){
                $otc_info->status = 0;
                $otc_info->down_time = date("Y-m-d H:i:s",time());
                $otc_info->save();
                //解冻剩余金额
                $freeze_id = FreezeLog::where(["relate"=>$otc_info->id,"type"=>"sale"])->first()->id;
                Helper::freeCoin($freeze_id);
            }
            elseif($otc_info->vc_less_amount==0)
            {
                //全部售完，下架
                $freeze_log= FreezeLog::where(["relate"=>$otc_info->id,"type"=>"sale"])->first();
                if($freeze_log->vc_amount - $freeze_log->vc_done_amount - $freeze_log->vc_lock_normal > 0)
                {
                    Helper::freeCoin($freeze_log->id);
                    $otc_info->status = 0;
                    $otc_info->down_time = date("Y-m-d H:i:s",time());
                    $otc_info->save();
                }
            }
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
     * @name down
     * @description 卖家下架
     * @param
     * otc_id：订单ID
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function down($param)
    {
        $user = $this->user;
        $otc_id = $param["otc_id"]; //挂单ID
        if(!$otc_id)
        {
            return $this->error("请输入挂单ID",FanweErrcode::OTC_ID_NOT_EXSITS);
        }
        $otc = Otc::where(["id"=>$otc_id,"user_id"=>$user->id,"status"=>1])->first(); //正常上架
        if(!$otc)
        {
            return $this->error("挂卖的交易单不存在",FanweErrcode::OTC_SALE_NOT_EXSITS);
        }
        $count = OtcOrder::where(['otc_id'=>$otc_id])->whereIn('status',[0,1])->count();
        if($count>0)
        {
            return $this->error('有未完成的订单,无法下架');
        }
        DB::beginTransaction();
        try{
            $otc->status = 0;
            $otc->down_time = date("Y-m-d H:i:s",time());
            $otc->save();
            //解冻剩余金额
            $freeze_id = FreezeLog::where(["relate"=>$otc->id,"type"=>"sale"])->first()->id;
            Helper::freeCoin($freeze_id);
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