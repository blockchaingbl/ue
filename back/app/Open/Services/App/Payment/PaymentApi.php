<?php
namespace App\Open\Services\App\Payment;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Payment;
use App\Http\Models\Web\Base;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;


class PaymentApi extends FanweAuthService
{

    /**
     * @name bindinfo
     * @description  用户绑定信息
     * @param
     * payment_key：支付类型（alipay：支付宝；weixin：微信；bankcard：银行卡），不传参，获取所有
     * @return bind_info
     * [
     *  "id" => 绑定信息ID,
     *  "payment_key" => 支付类型,
     *  "payment_account" => 账号,
     *  "payment_org" => 支付机构,
     *  "payment_receipt" => 收款人信息,
     *  "user_id" => 用户ID
     * ]
     */
    public function bindinfo($param)
    {
        $user = $this->user;
        $bind_info = Payment::getBindInfo($user->id,$param)->toArray();
        //格式化数据
        $bind_data = [];
        foreach ($bind_info as $item)
        {
            if($item["payment_key"]!="alipay"&&$item["payment_key"]!="weixin")
            {
                $bind_data["bankcard"] = $item;
            }
            else
            {
                $bind_data[$item["payment_key"]] = $item;
            }
        }
        $this->setData("bind_info",$bind_data);
        return $this->success();
    }

    /**
     * @name bind
     * @description  支付绑定
     * @param
     * payment_key：支付类型（alipay：支付宝；weixin：微信；bankcard：银行卡）
     * qrcode：二维码图片（绑定支付宝、微信时上传）
     * payment_org：支付机构（绑定银行卡时选择）
     * payment_account：账号
     * payment_receipt：收款人
     * security：资金密码
     * @return
     * 成功：无
     * 失败：返回错误码及提示
     */
    public function bind($param)
    {
        $user = $this->user;
        $payment_key = $param["payment_key"];
        $payment_org = $param["payment_org"];
        $payment_account = $param["payment_account"];
        $payment_receipt = $param["payment_receipt"];
        $qrcode = $param["qrcode"];
        $security = $param["security"];
        $branch_bank = trim($param['branch_bank']);

        if(!$payment_key)
        {
            return $this->error("请输入绑定类型",FanweErrcode::PAYMENT_TYPE_NOT_EXSITS);
        }
        if($payment_key!="alipay"&&$payment_key!="weixin"&&$payment_key!="bankcard")
        {
            return $this->error("绑定类型不存在",FanweErrcode::PAYMENT_TYPE_NOT_EXSITS);
        }
        if(!$qrcode&&($payment_key=="alipay"||$payment_key=="weixin"))
        {
            return $this->error("二维码图片未上传",FanweErrcode::PAYMENT_QRCODE_NOT_EXSITS);
        }
        if(!$payment_org&&$payment_key=="bankcard")
        {
            return $this->error("请选择开户银行",FanweErrcode::PAYMENT_BANKORG_NOT_EXSITS);
        }
        if(!$payment_account)
        {
            return $this->error("请填写账号",FanweErrcode::PAYMENT_ACCOUNT_NOT_EXSITS);
        }
        if(!$payment_receipt)
        {
            return $this->error("请填写收款人",FanweErrcode::PAYMENT_RECEIPT_NOT_EXSITS);
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
            $payment = Payment::where(["user_id"=>$user->id,"payment_key"=>$payment_key])->first();
            if(!$payment)
                $payment = new Payment();

            $payment->user_id = $user->id;
            $payment->payment_key = $payment_key;
            $payment->payment_account = $payment_account;
            $payment->payment_receipt = $payment_receipt;

            if($payment_key=="alipay")
            {
                $payment->payment_org = "支付宝";
                $payment->qrcode = $qrcode;
            }
            elseif($payment_key=="weixin")
            {
                $payment->payment_org = "微信支付";
                $payment->qrcode = $qrcode;
            }
            else
            {
                $payment->payment_org = $payment_org;
                $payment->branch_bank = $branch_bank;
            }

            $payment->save();
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
     * @name unbind
     * @description  支付解绑
     * @param
     * payment_key：支付类型（alipay：支付宝；weixin：微信；bankcard：银行卡）
     * @return
     * 成功：无
     * 失败：返回错误码及提示
     */
    public function unbind($param)
    {
        $user = $this->user;
        $payment_key = $param["payment_key"];

        if(!$payment_key)
        {
            return $this->error("请输入解绑类型",FanweErrcode::PAYMENT_TYPE_NOT_EXSITS);
        }
        if($payment_key!="alipay"&&$payment_key!="weixin"&&$payment_key!="bankcard")
        {
            return $this->error("解绑类型不存在",FanweErrcode::PAYMENT_TYPE_NOT_EXSITS);
        }

        DB::beginTransaction();
        try{
            Payment::where(["user_id"=>$user->id,"payment_key"=>$payment_key])->delete();
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