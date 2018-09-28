<?php
namespace App\Open\Services\App\Open;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CpLog;
use App\Http\Models\Exchange;
use App\Http\Models\Web\User;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\DB;


class MallApi extends FanweBaseService
{

    /**
     * @name exchange
     * @description 兑换币
     * @param
     * address：钱包地址
     * security：资金密码
     * amount：数量
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function exchange($param)
    {
        $address = $param["address"];
        $security = $param["security"];
        $amount = $param["amount"];
        $user = User::where("address",$address)->first();
        if(!$address)
        {
            return $this->error("请输入钱包地址",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        if(!$user)
        {
            return $this->error("钱包地址不存在",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        if(!$security)
        {
            return $this->error("请输入资金密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if(md5($security)!=$user->security)
        {
            return $this->error("资金密码错误",FanweErrcode::USER_SECURITY_ERROR);
        }
        if(!$amount)
        {
            return $this->error("请输入兑换数量",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if($amount>$user->vc_total)
        {
            return $this->error("兑换数量不足",FanweErrcode::USER_EXCHANGE_AMOUNT_OVER);
        }
        DB::beginTransaction();
        try{
            $exchange = new Exchange();
            $exchange->user_id = $user->id;
            $exchange->user_address = $user->address;
            $exchange->create_time = date("Y-m-d H:i:s",time());
            $exchange->api = "o2o";
            $exchange->memo = "o2o平台兑换币";
            $exchange->vc_amount = $amount;
            //先扣除不可交易的部分
            $diff_amount = $amount - $user->vc_untrade;
            if($diff_amount>0)
            {
                $exchange->vc_normal = $diff_amount;
                $exchange->vc_untrade = $user->vc_untrade;
            }
            else
            {
                $exchange->vc_untrade = $amount;
            }
            $exchange->save();
            //生成支出
            Helper::expendCoin("exchange",$exchange->id);
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
     * @name increasecp
     * @description 增加算力
     * @param
     * address：钱包地址
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function increasecp($param)
    {
        $address = $param["address"];
        $user = User::where("address",$address)->first();
        if($address && $user)
        {
            $last_time = CpLog::where(["user_id"=>$user->id,"type"=>0,"api"=>"o2o"])->max("create_time");
            if(!$last_time)
            {
                $last_time = "2000-00-00 00:00:00";
            }
            //请求O2O查账接口，确认消费金额
            $oto_api = "http://o2oxsl.fanwe.net/mapi/index.php?ctl=deal&act=bdc_items";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $oto_api);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            $params["address"] = $user->address;
            $params["last_time"] = $last_time;
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            $res = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($res,true);
            if($res["errcode"]==0)
            {
                $amount = $res["amount"]; //消费金额
                if($amount>0)
                {
                    DB::beginTransaction();
                    try{
                        $vcoin = $amount/db_config("COIN_PRICE"); //币的个数
                        $cp_amount = $vcoin*db_config("YIELD_RATE")*db_config("CP_COIN"); //算力
                        Helper::GrantCp($user->id,0,$cp_amount,"o2o");
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
        }
    }

}