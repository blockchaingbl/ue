<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\Manage\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends AuthBaseController
{

    private $redirectTo = "/";

    public function __construct()
    {

    }

    public function login()
    {
        $app_name = config("app.name");
        return view("manage.admin.login",["app_name"=>$app_name]);
    }

    // 登录操作
    public function dologin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ],[
            'required' => '不能为空'
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }

        if(!$this->checkCaptcha($request,$request->input("uuid"),"default")){
            $validator->errors()->add('login_message', '验证码错误!');
            return redirect()->back()->withErrors($validator->errors());
        }

        if (Auth::attempt(['username'=>$request->input("username"),'password'=>$request->input("password")],false))
        {
            return redirect($this->redirectTo);
        }
        else
        {
            $validator->errors()->add('login_message', '密码错误!');
            return redirect()->back()->withErrors($validator->errors());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect($this->redirectTo);
    }


}
