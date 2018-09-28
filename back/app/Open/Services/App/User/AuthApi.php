<?php
namespace App\Open\Services\App\User;

use App\FanweErrcode;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use App\Open\Services\Helper;
use Auth;


class AuthApi extends FanweAuthService
{

    /**
     * @name logout
     * @description 登出操作
     * @param $param
     * @return array
     */
    public function logout($param)
    {
        User::where("id",$this->user->id)->update(["auth_token"=>null,"auth_expire"=>null]);
        return $this->success("成功退出");
    }

}