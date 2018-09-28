<?php
namespace App\Open\Services\App\Open;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Exchange;
use App\Http\Models\PlatformOpenid;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\DB;


class ActApi extends FanweBaseService
{

    /**
     * @name cp
     * @description 算力接口
     * @param
     * openid  用户的唯一标准
     * amount 增加的算力值
     * @return
     *
     */
    public function cp($param)
    {
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        $amount = $param['amount'];
        if($amount<=0){
            return $this->error("算力数量不正确");
        }

        DB::beginTransaction();
        try{
            Helper::GrantCp($user->id,0,$amount,"mall");
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
        return $this->success();
    }

    /**
     * @name pay
     * @description 资产支付接口
     * @param
     * openid    账户的openid
     * security  资金密码
     * amount    支付金额
     * memo  支出日志说明
     * @return
     */
    public function pay($param){
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }

        $security = $param['security'];
        $amount = $param['amount'];
        $memo = $param['memo'];

        if(!$security)
        {
            return $this->error("请输入资金密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if(md5($security)!=$user->security)
        {
            return $this->error("资金密码错误",FanweErrcode::USER_SECURITY_ERROR);
        }
        if($amount<=0)
        {
            return $this->error("支付金额出错");
        }
        if($amount>$user->vc_total)
        {
            return $this->error("余额不足",FanweErrcode::USER_EXCHANGE_AMOUNT_OVER);
        }

        DB::beginTransaction();
        try{
            $exchange = new Exchange();
            $exchange->user_id = $user->id;
            $exchange->user_address = $user->address;
            $exchange->create_time = date("Y-m-d H:i:s",time());
            $exchange->api = "mall";
            $exchange->memo = $memo;
            $exchange->vc_amount = $amount;
            $exchange->coin_type = 0;

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
            $this->setData("exchange_id",$exchange->id);  //流水单号
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }

    }
}