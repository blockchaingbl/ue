<?php

namespace App\Http\Controllers\Web;


use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Web\User;
use App\Library\Weixin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Validator;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    //登录页
    public function login()
    {
        if(Auth::user())
        {
            return redirect(url("/"));
        }
        return view("web.login");
    }

    //注册页
    public function register()
    {
        return view("web.register");
    }

    //密码找回页
    public function forget(Request $request)
    {
        return view("web.forget");
    }

    //用户协议页
    public function agreement()
    {
        return view("web.agreement");
    }

    //登录操作
    public function dologin(Request $request)
    {
        $auth = Helper::invoke("app.user/user/login_by_username",["username"=>$request->input('username'),"password"=>$request->input('password')]);
        if($auth['errcode'])
        {
            return $auth;
        }
        else
        {
            if (!Auth::attempt(array('auth_token' => $auth['data']['_token'],"password"=>$request->input('password')), $request->input("is_remember"))) {
                $rs["errcode"] =FanweErrcode::SYSTEM_ERROR;
                $rs["message"] = "未知错误";
                return $rs;
            }
        }
        if($request['jumpUrl'])
            $auth['url'] = urldecode($request['jumpUrl']);
        else
            $auth['url'] = url("/");
        return $auth;
    }

    //注册操作
    public function doregister(Request $request)
    {
        $rs = Helper::invoke("app.user/user/register",$request);
        if($rs['errcode']==FanweErrcode::SYSTEM_SUCCESS){
            if (!Auth::attempt(array('username' => $rs['data']['username'], 'password' => $request->input('password')), 0)) {
                $rs["errcode"] = FanweErrcode::SYSTEM_ERROR;
                $rs["message"] = "密码错误";
            }
            elseif (!Auth::attempt(array('email' => $rs['data']['email'], 'password' => $request->input('password')), 0))
            {
                $rs["errcode"] = FanweErrcode::SYSTEM_ERROR;
                $rs["message"] = "密码错误";
            }
            else{
                if($request['jumpUrl'])
                    $rs['url'] = urldecode($request['jumpUrl']);
                else
                    $rs['url'] = url("/");
            }
        }
        return $rs;
    }

    //密码找回
    public function doforget(Request $request)
    {
//        $rs = User::resetPasswordByEmail($request);
//
//        if($rs['errcode']==FanweErrcode::SYSTEM_SUCCESS){
//            $data['username'] = $request['email'];
//            $data['password'] = $request['password'];
//            $rss = User::checklogin($data);
//            if (!Auth::attempt(array('email' => $rss['data']['email'], 'password' => $request['password']), 0)) {
//                $rs["errcode"] = FanweErrcode::SYSTEM_ERROR;
//                $rs["message"] = "密码错误";
//            }
//            else{
//                if($request['jumpUrl'])
//                    $rs['url'] = urldecode($request['jumpUrl']);
//                else
//                    $rs['url'] = url("/");
//            }
//        }
//
//        return $rs;
    }

    //退出操作
    public function logout()
    {
        if (Auth::check())
        {
            Helper::invoke("app.user/auth/logout");
            Auth::logout();
        }
        return redirect("/");
    }

    //资料修改
    public function updateinfo(Request $request)
    {

    }


}
