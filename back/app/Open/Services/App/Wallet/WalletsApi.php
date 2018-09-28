<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\TransferLog;
use App\Http\Models\UserWallet;
use App\Http\Models\Web\User;
use App\Http\Models\Withdraw;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\DB;


class WalletsApi extends FanweAuthService
{

    /**
     * @name index
     * @description 用户绑定钱包列表
     * @param 无，只需要授权有效即可
     * @return $wallets
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function index($param)
    {
        $user = $this->user;
        $list = UserWallet::where("user_id",$user->id)->get()->toArray();
        $wallets = [];
        if($list)
        {
            foreach ($list as $item)
            {
                $wallet["name"] = $item["name"];
                $wallet["address"] = $item["address"];
                $wallet["enc_privateKey"] = $item["enc_privatekey"];
                $wallet["wallet_pwd"] = $item["wallet_pwd"];
                $wallets[$item["address"]] = $wallet;
            }
        }
        $this->setData("wallets",$wallets);
        return $this->success();
    }

    /**
     * @name bind
     * @description 用户钱包绑定
     * @param
     * name：钱包名称
     * address：钱包地址
     * enc_privatekey：加密私钥
     * * wallet_pwd：md5密码
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function bind($param)
    {
        $user = $this->user;
        $name = $param["name"];
        $address = $param["address"];
        $enc_privatekey = $param["enc_privatekey"];
        $wallet_pwd = $param['wallet_pwd'];
        if(!$name)
        {
            return $this->error("请输入钱包名称",FanweErrcode::USER_WALLETNAME_NOT_EXIST);
        }
        if(!$address)
        {
            return $this->error("请输入钱包地址",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        $user_address = UserWallet::where("address",$address)->where("user_id","<>",$user->id)->first();
        if($user_address)
        {
            return $this->error("该钱包地址已被其他用户绑定",FanweErrcode::USER_ADDRESS_EXIST);
        }
        if(!$enc_privatekey)
        {
            return $this->error("请输入加密私钥",FanweErrcode::PRIVATEKEY_NOT_EXSITS);
        }

        DB::beginTransaction();
        try{
            $user_wallet = UserWallet::where(["address"=>$address,"user_id"=>$user->id])->first();
            if(!$user_wallet)
            {
                $user_wallet = new UserWallet();
                $user_wallet->user_id = $user->id;
                $user_wallet->address = $address;
            }
            $user_wallet->name = $name;
            $user_wallet->enc_privatekey = $enc_privatekey;
            $user_wallet->wallet_pwd = $wallet_pwd;
            $user_wallet->save();
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
     * @name setname
     * @description 设置钱包名称
     * @param
     * address：钱包地址
     * name：钱包名称
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function setname($param)
    {
        $user = $this->user;
        $address = $param["address"];
        $name = $param["name"];
        if(!$address)
        {
            return $this->error("请输入钱包地址",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        if(!$name)
        {
            return $this->error("请输入钱包名称",FanweErrcode::USER_WALLETNAME_NOT_EXIST);
        }
        DB::beginTransaction();
        try{
            $wallet = UserWallet::where(["user_id"=>$user->id,"address"=>$address])->first();
            $wallet->name = $name;
            $wallet->save();
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
     * @name setpassword
     * @description 设置密码
     * @param
     * address：钱包地址
     * enc_privatekey：加密私钥
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function setpassword($param)
    {
        $user = $this->user;
        $address = $param["address"];
        $enc_privatekey = $param["enc_privatekey"];
        $wallet_pwd = $param['wallet_pwd'];
        if(!$address)
        {
            return $this->error("请输入钱包地址",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        if(!$enc_privatekey)
        {
            return $this->error("请输入加密私钥",FanweErrcode::PRIVATEKEY_NOT_EXSITS);
        }
        DB::beginTransaction();
        try{
            $wallet = UserWallet::where(["user_id"=>$user->id,"address"=>$address])->first();
            $wallet->enc_privatekey = $enc_privatekey;
            $wallet->wallet_pwd = $wallet_pwd;
            $wallet->save();
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
     * @name delete
     * @description 删除钱包账户
     * @param
     * address：钱包地址
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function delete($param)
    {
        $user = $this->user;
        $address = $param["address"];
        if(!$address)
        {
            return $this->error("请输入钱包地址",FanweErrcode::USER_ADDRESS_NOT_EXIST);
        }
        DB::beginTransaction();
        try{
            UserWallet::where(["user_id"=>$user->id,"address"=>$address])->delete();
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