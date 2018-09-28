<?php
namespace App\Open\Services\App\Open;

use App\FanweErrcode;
use App\Http\Models\PlatformOpenid;
use App\Open\Services\FanweBaseService;



class UserApi extends FanweBaseService
{

    /**
     * @name info
     * @description 获取用户信息
     * @param openid
     * @return account_info
     * [
    "id"=> 用户ID,
    "security"=> 加密后的资金密码,
    "username"=> 用户名,
    "mobile"=>手机号,
    "avatar"=>头像
    ];
     *
     */
    public function info($param)
    {
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        $account_info =  [
            "id"=>$user->id,
            "username"=>$user->username,
            "mobile"=>$user->mobile,
            "avatar"=>img($user->avatar,200,200),
            'security'=>$user->security
        ];
        $this->setData("account_info",$account_info);
        return $this->success();
    }

    /**
     * @name logout
     * @description 登出
     * @param openid
     * @return 成功与否
     * 成功 null
     * 失败 错误提示和状态码
     */
    public function logout($param){
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        $user->auth_expire = time();
        $result = $user->save();
        if($result){
            return $this->success();
        }else{
            return $this->error();
        }
    }
}