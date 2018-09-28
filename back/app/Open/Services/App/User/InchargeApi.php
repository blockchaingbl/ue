<?php
namespace App\Open\Services\App\User;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\CpLog;
use App\Http\Models\Exchange;
use App\Http\Models\InchargeLog;
use App\Http\Models\SubscribeToken;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\DB;


class InchargeApi extends FanweAuthService
{

    /**
     * @name index
     * @description 充值
     * @param
     * token_symbol：币名称
     * amount：钱包地址
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function index($param)
    {
        $user = $this->user;
        $token_symbol = $param["token_symbol"];
        $token_amount = $param["token_amount"];
        $incharge_amount = $param["incharge_amount"];

        $coinType = CoinType::where("coin_unit",$token_symbol)->first();
        if(!$coinType)
        {
            $coin_type = 0;
        }
        else
        {
            $coin_type = $coinType->id;
        }
        if(!$token_symbol)
        {
            return $this->error("请输入币的类型",FanweErrcode::COIN_TYPE_NOT_EXSITS);
        }
        if($token_symbol==db_config("COIN_UNIT"))
        {
            return $this->error("资产充值不允许选择平台币",FanweErrcode::COIN_TYPE_NOT_EXSITS);
        }
        if(!$incharge_amount)
        {
            return $this->error("请输入充值金额",FanweErrcode::INCHARGE_AMOUNT_NOT_EXSITS);
        }
        $userAsset = UserAsset::where(["user_id"=>$user->id,"coin_type"=>$coin_type])->first();
        if($token_amount>$userAsset->vc_total)
        {
            return $this->error("资产不足，无法充值",FanweErrcode::INCHARGE_AMOUNT_OVER);
        }
        $platform_coin_price = floatval(db_config("COIN_PRICE")); //平台币单价
        DB::beginTransaction();
        try{
            $token_config = SubscribeToken::where("token_symbol",$token_symbol)->first();
            $amount = $incharge_amount;
            $token_amount = $amount*($platform_coin_price/floatval($token_config->incharge_rate));
            $token_amount = Helper::trimNumber($token_amount,5);

            //生成收入记录（资产只充值平台币）
            $incharge_log = new InchargeLog();
            $incharge_log->token_symbol = $token_symbol;
            $incharge_log->token_amount = $token_amount;
            $incharge_log->amount = $amount;
            $incharge_log->create_time = date("Y-m-d H:i:s",time());
            $incharge_log->user_id = $user->id;
            $incharge_log->save();
            Helper::incomeCoin("incharge",$incharge_log->id);

            //生成支出记录
            $exchange = new Exchange();
            $exchange->user_id = $user->id;
            $exchange->create_time = date("Y-m-d H:i:s",time());
            $exchange->vc_amount = $token_amount;
            $exchange->vc_untrade = $token_amount;
            $exchange->api = "incharge";
            $exchange->memo = "充值 ".$incharge_amount." ".db_config("COIN_UNIT");
            $exchange->coin_type = $coin_type;
            $exchange->save();
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