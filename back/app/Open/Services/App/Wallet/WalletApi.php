<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\TransferLog;
use App\Http\Models\UserAsset;
use App\Http\Models\UserWallet;
use App\Http\Models\Web\User;
use App\Http\Models\Withdraw;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\DB;


class WalletApi extends FanweAuthService
{

    /**
     * @name withdraw
     * @description 上链申请
     * @param
     * coin_type：币类型（0表示基础币），不传参则默认基础币
     * address : 上链地址
     * amount：上链数量
     * @return
     * 成功：返回withdraw_id 上链申请ID
     * 失败：返回错误码及提示
     */
    public function withdraw($param)
    {
        $user = $this->user;

        
        $coin_type = $param["coin_type"];
        if($coin_type==0){
            $vc_total = $user->vc_normal; //余额
            $min_withdraw = db_config("MIN_WITHDRAW"); //最小上链金额
            $withdraw_open = db_config("WITHDRAW_OPEN"); //上链开关
            $withdraw_rate = db_config('WITHDRAW_RATE');
        }else{
            $coin_type_info =  CoinType::find($coin_type);
            $coinAsset  = UserAsset::where([
                'user_id'=>$user->id,
                'coin_type'=>$coin_type
            ])->first();
            $min_withdraw = $coin_type_info->min_withdraw;
            $vc_total = $coinAsset->vc_total;
            $withdraw_open = $coin_type_info->withdraw_open;
            $withdraw_rate = $coin_type_info->withdraw_rate;
        }
        $address = $param['address'];
        $amount = $param["amount"];
        $withdraw_fee = $amount * $withdraw_rate;
        $withdraw_fee = $withdraw_fee;
	    if(!$withdraw_open)
        {
            return $this->error("上链暂未开放",FanweErrcode::WITHDRAW_ISCLOSE);
        }
        if(!$address)
        {
            return $this->error("请选择钱包",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        if($coin_type=="")
        {
            return $this->error("请输入币类型",FanweErrcode::COIN_TYPE_NOT_EXSITS);
        }
        if(!$amount)
        {
            return $this->error("请输入上链的数量",FanweErrcode::USER_WITHDRAW_AMOUNT_NOT_EXIST);
        }
        if($amount<$min_withdraw)
        {
            return $this->error("数量低于平台最小上链数量",FanweErrcode::USER_WITHDRAW_AMOUNT_LESS);
        }
        if($amount>$vc_total)
        {
            return $this->error("可上链数量不足",FanweErrcode::USER_WITHDRAW_AMOUNT_OVER);
        }
        $check_limit = Withdraw::checkLimit($user,$coin_type,$amount);
	    if($check_limit['errcode']!=0)
        {
            return $this->error($check_limit['message'],FanweErrcode::USER_WITHDRAW_OTC_LIMIT);
        }
        DB::beginTransaction();
        try{
            $withdraw = new Withdraw();
            $withdraw->coin_type = $coin_type;
            $withdraw->user_id = $user->id;
            $withdraw->create_time = date("Y-m-d H:i:s",time());
            $withdraw->vc_amount = $amount;
            $withdraw->withdraw_fee = $withdraw_fee;
            if($coin_type==0){
                $withdraw->vc_normal = $amount;
            }else{
                $withdraw->vc_untrade = $amount;
            }

            $withdraw->to_address = $address;
            $withdraw->save();
            Helper::freezeCoin("withdraw",$withdraw->id);
            DB::commit();
            $this->setData("withdraw_id",$withdraw->id);
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
     * @name withdrawlog
     * @description 获取用户上链记录
     * @param
     * coin_type：币类型（0表示基础币），不传参则默认基础币
     * status：状态(0：待审核；1：已发放；2：已拒绝)，不传参则获取全部
     * page：页数
     * @return withdraw_log
     * [
     * "id" => 记录ID,
     * "user_id" => 用户ID,
     * "vc_amount" => 金额,
     * "vc_normal" => 可交易,
     * "vc_untrade" => 不可交易,
     * "create_time" => 时间,
     * "status" => 状态(0：待审核；1：已发放；2：已拒绝)
     * ]
     */
    public function withdrawlog($param)
    {
        $user = $this->user;
        $coin_type = $param["coin_type"];
        $status = $param["status"]>=0?$param["status"]:-1;
        $withdraw_log = Withdraw::getWithdrawLog($user->id,$coin_type,$status);
        //格式化数据
        $withdraw_log = $withdraw_log->toArray()["data"];
        $withdraw_data = [];
        foreach($withdraw_log as $item)
        {
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["vc_normal"] = Helper::formatCoin($item["vc_normal"],1);
            $item["vc_untrade"] = Helper::formatCoin($item["vc_untrade"],1);
            array_push($withdraw_data,$item);
        }
        $this->setData("withdraw_log",$withdraw_data);
        return $this->success();
    }

    /**
     * @name transfer
     * @description 获取用户转账记录
     * @param
     * transfer_id: （转账记录ID）传参则获取某一条记录的详情
     * page：页数
     * @return transfer_list
     * [
     * "id" => 转账记录ID,
     * "from" => 付款人ID,
     * "to" => 收款人ID,
     * "from_address" => 付款人地址,
     * "to_address" => 收款人地址,
     * "vc_amount" => 金额,
     * "create_time" => 时间,
     * "log" => 日志
     * ]
     */
    public function transfer($param)
    {
        $user = $this->user;
        $transfer_list = TransferLog::getTransferLog($user->id,$param);
        //格式化数据
        $transfer_list = $transfer_list->toArray()["data"];
        $transfer_data = [];
        foreach($transfer_list as $item)
        {
            if($item["from"]==$user->id)
            {
                $item["vc_amount"] = "-".Helper::formatCoin($item["vc_amount"],1);
                $item["log"] = "发送到：".$item["to_address"];
            }
            elseif($item["to"]==$user->id)
            {
                $item["vc_amount"] = "+".Helper::formatCoin($item["vc_amount"],1);
                $item["log"] = "接收自：".$item["from_address"];
            }
            array_push($transfer_data,$item);
        }
        $this->setData("transfer_list",$transfer_data);
        return $this->success();
    }

    /**
     * @name send
     * @description 发送币
     * @param
     * amount：发送数量
     * to_address: 对方钱包地址
     * security: 资金密码
     * memo: 备注
     * @return
     * 成功：返回transfer_id 转账ID
     * 失败：返回错误码及提示
     */
    public function send($param)
    {
        $user = $this->user;
        $amount = $param["amount"];
        $to_address = $param["to_address"];
        $security = $param["security"];
        $memo = $param["memo"];
        if(!$amount)
        {
            return $this->error("请输入要发送的数量",FanweErrcode::SEND_AMOUNT_NOT_EXSITS);
        }
        if($amount>$user->vc_total)
        {
            return $this->error("要发送的数量超出剩余数量",FanweErrcode::SEND_AMOUNT_OVER);
        }
        if(!$to_address)
        {
            return $this->error("请输入对方的钱包地址",FanweErrcode::TO_ADDRESS_NOT_EXSITS);
        }
        $to_user = User::where("address",$to_address)->first();
        if(!$to_user)
        {
            return $this->error("该钱包用户不存在",FanweErrcode::TO_USER_NOT_EXSITS);
        }
        if($to_address==$user->address)
        {
            return $this->error("不允许发送给自己",FanweErrcode::NOTALLOW_SEND_OWNER);
        }
        if(!$security)
        {
            return $this->error("请输入资金密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if($security!=$user->security)
        {
            return $this->error("资金密码错误",FanweErrcode::USER_SECURITY_ERROR);
        }
        DB::beginTransaction();
        try{
            $transfer_log = new TransferLog();
            $transfer_log->from = $user->id;
            $transfer_log->to = $to_user->id;
            $transfer_log->from_address = $user->address;
            $transfer_log->to_address = $to_user->address;
            $transfer_log->vc_amount = $amount;
            if(config("app.otc"))
            {
                //开启otc
                //先扣除不可交易的部分
                $diff_amount = $amount - $user->vc_untrade;
                if($diff_amount>0)
                {
                    $transfer_log->vc_normal = $diff_amount;
                    $transfer_log->vc_untrade = $user->vc_untrade;
                }
                else
                {
                    $transfer_log->vc_untrade = $amount;
                }
            }
            else
            {
                //关闭otc
                $transfer_log->vc_untrade = $amount;
            }

            $transfer_log->create_time = date("Y-m-d H:i:s");
            if($memo)
            {
                $transfer_log->memo = $memo;
            }
            $transfer_log->save();
            //发送人生成支出
            Helper::expendCoin("transfer",$transfer_log->id);
            //接收人生成收入
            Helper::incomeCoin("transfer",$transfer_log->id);
            DB::commit();
            $this->setData("transfer_id",$transfer_log->id);
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name bind
     * @description 用户钱包绑定
     * @param
     * address：钱包地址
     * privatekey：私钥
     * security：密码
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function bind($param)
    {
        $user = $this->user;
        $address = $param["address"];
        $privatekey = $param["privatekey"];
        $security = $param["security"];
        if(!$address)
        {
            return $this->error("请输入钱包地址",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        $user_address = User::where("address",$address)->where("id","<>",$user->id)->first();
        if($user_address)
        {
            return $this->error("该钱包地址已被其他用户绑定",FanweErrcode::USER_ADDRESS_EXIST);
        }
        if(!$privatekey)
        {
            return $this->error("请输入私钥",FanweErrcode::PRIVATEKEY_NOT_EXSITS);
        }
        if(!$security)
        {
            return $this->error("请输入密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        DB::beginTransaction();
        try{
            $user = User::where("id",$user->id)->first();
            $user->address = $address;
            $user->privatekey = $privatekey;
            $user->security = md5($security);
            $user->save();
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
     * @name setsecurity
     * @description 设置密码
     * @param
     * security：原密码
     * new_security：新密码
     * privatekey：新的私钥
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function setsecurity($param)
    {
        $user = $this->user;
        $security = $param["security"];
        $new_security = $param["new_security"];
        $privatekey = $param["privatekey"];
        if(!$security)
        {
            return $this->error("请输入原密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if(md5($security)!=$user->security)
        {
            return $this->error("原密码错误",FanweErrcode::USER_SECURITY_ERROR);
        }
        if(!$new_security)
        {
            return $this->error("请输入新密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if(!$privatekey)
        {
            return $this->error("请输入新的私钥",FanweErrcode::USER_PRIVATEKEY_NOT_EXSITS);
        }
        DB::beginTransaction();
        try{
            $user = User::where("id",$user->id)->first();
            $user->security = md5($new_security);
            $user->privatekey = $privatekey;
            $user->save();
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