<?php
namespace App\Open\Services\App\User;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Common\SmsCode;
use App\Http\Models\PlatformOpenid;
use App\Http\Models\Web\User;
use App\Library\CurlInvoker;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserApi extends FanweBaseService
{

    /**
     * @name register
     * @description 会员注册接口，用户名与密码的最简方式注册
     * @param
     * username:用户名
     * mobile:手机号
     * verifycode:验证码，与登录验证码共用
     * @return
     * 代码分别为 MOBILE_EMPTY,MOBILE_CODE_EMPTY,USER_IS_FORBID,MOBILE_CODE_ERROR,SYSTEM_ERROR，具体的值请参阅文档
     * 注册成功后会自动登录，登录成功后返回如下
     * _token:会员的授权令牌
     */
    public function register($param)
    {
//        $total = 9000; //总返还
//        for($i=1;$i<=365*10;$i++)
//        {
//            $each = $total / (365);
//            echo "第".$i."天".$total." ".$each."<br />";
//            $total-=$each;
//        }
//        exit;

        if(!config("app.debug")&&!Helper::isApp())
        {
            return $this->error("请使用APP注册");
        }

        $mobile = $param['mobile'];
        $verifycode = $param['verifycode'];
        $invite_code = $param['invite_code'];
        $mobile_code = $param['mobile_code'];
        if(!$mobile)
        {
            return $this->error("请输入手机号码",FanweErrcode::MOBILE_EMPTY);
        }
        if(!$verifycode)
        {
            return $this->error("请输入手机验证码",FanweErrcode::MOBILE_CODE_EMPTY);
        }

        $user = User::where("mobile",$mobile)->first();
        if($user)
        {
            //会员存在直接登录
            return $this->login($param);
        }

        //开始注册
        if(!SmsCode::checkRegisterCode($mobile,$verifycode))
        {
            return $this->error("手机验证码无效",FanweErrcode::MOBILE_CODE_ERROR);
        }
        if($invite_code)
        {
            $pid = intval(User::getUserByInviteCode($invite_code));
        }else{
            $pid =0;
        }
        if($invite_code&&!$pid){
            return $this->error("邀请码错误",FanweErrcode::USER_INVITE_CODE_ERROR);
        }
        //密码暂时无用， 统一使用一个随机密码
        $user = ["username"=>$param['username'],"password"=>$param['password'],"mobile"=>$mobile,"pid"=>$pid,"mobile_code"=>$mobile_code];
        $res = User::createUser($user);
        if($res['errcode']>0)
        {
            return $this->error($res['message'],$res['errcode']);
        }

        //给推荐人返还算力
        $cp_amount = db_config("INVITE_CP");
        if($pid>0)
        {
            Helper::GrantCp($pid,1,$cp_amount,"invite");
        }

        //注册成功自动登录
        return $this->login($param);
    }

    /**
     * @name login
     * @description 会员短信登录接口
     * @param
     * mobile: 手机号码
     * verifycode: 验证码
     * auth: 是否为授权，授权只验证，不登录，不更新token
     * @return
     * 当errcode为 USER_NOT_EXSITS:50001 表示为手机未注册，将需要再客户端进入注册流程
     * 其他错代码分别为 MOBILE_EMPTY,MOBILE_CODE_EMPTY,USER_IS_FORBID,MOBILE_CODE_ERROR,SYSTEM_ERROR，具体的值请参阅文档
     * 登录成功后返回如下
     * _token:会员的授权令牌
     */
    public function login($param)
    {
        $mobile = $param['mobile'];
        $verifycode = $param['verifycode'];
        $auth = $param['auth'];

        if(!$mobile)
        {
            return $this->error("请输入手机号码",FanweErrcode::MOBILE_EMPTY);
        }
        if(!$verifycode)
        {
            return $this->error("请输入手机验证码",FanweErrcode::MOBILE_CODE_EMPTY);
        }
        if(!SmsCode::checkRegisterCode($mobile,$verifycode))
        {
            return $this->error("手机验证码无效",FanweErrcode::MOBILE_CODE_ERROR);
        }

        $user = User::where("mobile",$mobile)->first();
        if(!$user)
        {
            return $this->error("手机从未注册过",FanweErrcode::USER_NOT_EXSITS);
        }
        if(!$user->status)
        {
            return $this->error("账户已被禁用",FanweErrcode::USER_IS_FORBID);
        }
        if(!$auth||!$user->auth_token)
        {
            $lifetime = config("app.auth_time");
            //生成token
            $token = md5(time()."_".$user->id);
            $expire = time() + intval($lifetime)*60;

            $user->auth_token = $token;
            $user->auth_expire = $expire;
            $rs = $user->save();
            if(!$rs)
            {
                return $this->error("登录失败，未知的错误");
            }
            Helper::invoke("app.user/account/sign",["_user_token"=>$token]);
            $this->setData("_user_token",$token);
        }
        else
        {
            $this->setData("_user_token",$user->auth_token);
        }
        //清掉验证码
        SmsCode::clearCode("register",$mobile);
        return $this->success("登录成功");
    }

    /**
     * @name loginforpwd
     * @description 会员密码登录接口
     * @param
     * username: 用户名或手机号
     * password: 登录密码
     * @return
     * 错误代码分别为 MOBILE_EMPTY,MOBILE_CODE_EMPTY,USER_IS_FORBID,MOBILE_CODE_ERROR,SYSTEM_ERROR，具体的值请参阅文档
     * 登录成功后返回如下
     * _token:会员的授权令牌
     */
    public function loginforpwd($param)
    {
        $username = $param['username'];
        $password = $param['password'];
        $auth = $param['auth'];

        if(!$username)
        {
            return $this->error("请输入用户名或手机号",FanweErrcode::MOBILE_EMPTY);
        }
        $user = User::where("username",$username)->orWhere("mobile",$username)->first();
        if(!$user)
        {
            return $this->error("该账号未注册",FanweErrcode::USER_NOT_EXSITS);
        }
        if(!$user->status)
        {
            return $this->error("账号已被禁用",FanweErrcode::USER_IS_FORBID);
        }
        if(!$password)
        {
            return $this->error("请输入登录密码",FanweErrcode::PASSWORD_FORMAT_ERROR);
        }
        if(!Hash::check($password,$user->password))
        {
            return $this->error("登录密码错误",FanweErrcode::PASSWORD_ERROR);
        }
        if(!$auth||!$user->auth_token)
        {
            $lifetime = config("app.auth_time");
            //生成token
            $token = md5(time()."_".$user->id);
            $expire = time() + intval($lifetime)*60;
            $user->auth_token = $token;
            $user->auth_expire = $expire;
            $rs = $user->save();
            if(!$rs)
            {
                return $this->error("登录失败，未知的错误");
            }
            $this->setData("_user_token",$token);
            Helper::invoke("app.user/account/sign",["_user_token"=>$token]);
        }
        else
        {
            $this->setData("_user_token",$user->auth_token);
        }
        return $this->success("登录成功");
    }

    /**
     * @name resetpwd
     * @description 重置密码
     * @param
     * mobile: 手机号码
     * verifycode: 验证码
     * password: 新的密码
     * @return
     * 错误代码分别为 MOBILE_EMPTY,MOBILE_CODE_EMPTY,USER_IS_FORBID,MOBILE_CODE_ERROR,SYSTEM_ERROR，具体的值请参阅文档
     * 重置成功返回0
     */
    public function resetpwd($param)
    {
        $mobile = $param['mobile'];
        $verifycode = $param['verifycode'];
        $password = $param['password'];
        if(!$mobile)
        {
            return $this->error("请输入手机号码",FanweErrcode::MOBILE_EMPTY);
        }
        if(!User::checkMobile($mobile)){
            return $this->error("手机号码格式错误","mobile",FanweErrcode::MOBILE_FROMAT_ERROR);
        }
        $user = User::where("mobile",$mobile)->first();
        if(!$user)
        {
            return $this->error("该手机未注册",FanweErrcode::USER_NOT_EXSITS);
        }
        if(!$user->status)
        {
            return $this->error("账户已被禁用",FanweErrcode::USER_IS_FORBID);
        }
        if(!$verifycode)
        {
            return $this->error("请输入手机验证码",FanweErrcode::MOBILE_CODE_EMPTY);
        }
        if(!SmsCode::checkRegisterCode($mobile,$verifycode))
        {
            return $this->error("手机验证码无效",FanweErrcode::MOBILE_CODE_ERROR);
        }
        if(!$password)
        {
            return $this->error("请输入新的密码",FanweErrcode::PASSWORD_FORMAT_ERROR);
        }
        if(!User::checkPassword($password)){
            return $this->error("密码格式不能少于6位,大于16位","password",FanweErrcode::PASSWORD_FORMAT_ERROR);
        }
        $res = User::where(['mobile'=>$mobile])->update(["password"=>bcrypt($password),"password_level"=>User::password_level($password)]);
        if(!$res)
        {
            return $this->error("重置失败，未知的错误");
        }
        return $this->success("重置成功");
    }

    /**
     * @name auth
     * @description 用于第三方平台接入本平台的授权的请求兼登录请求
     * @param $param
     * security_params: 加密过的参数，包含callback 回调地址
     * platform_id：平台ID用于识别对接的平台
     *
     * login_type: 登录方式('smsLogin':'短信登录','pwdLogin':'密码登录')
     * dologin:是否主动登录，用于当未登录时，登录完再授权
     * 当dologin为1时，另外需要与user/login接口相同的参数
     *
     * @return array
     * jumpurl: 回调的地址，用于用户点击授权登录时跳转到该地址
     *  loginstatus: 登录状态，如果为0，页面显示的是登录操作
        :platform_icon:第三方平台接入的图标
        platform_name第三方平台接入的名称
        site_icon 当前平台的图标
        site_name 当前平台的名称
     */
    public function auth($param){
        $security_params = $param['security_params'];
        $platform_id = $param['platform_id'];
        $platform = Helper::load_api_key($platform_id);
        if(!$platform)
        {
            return ['errcode'=>FanweErrcode::OPEN_PLATFORMID_ERROR,"message"=>"平台ID错误"];
        }
        $res =  CurlInvoker::decodeSecurityParams($security_params,$platform_id);
        if(!$res)
        {
            return $this->error("非法请求");
        }

        $this->setData("platform_name",$platform->platform_name);
        $this->setData("platform_icon",$platform->platform_icon);

        $this->setData("site_name",config("app.name"));
        $this->setData("site_icon",url("icon.png"));

        $user = $this->init_user($param);
        if($user['errcode']&&$param['dologin']==1){
            //看看有没有登录的参数
            if($param['login_type']=='smsLogin')
            {
                //短信登录
                $param['auth'] = 1;
                $result = $this->login($param);
            }
            else
            {
                //密码登录
                $param['auth'] = 1;
                $result = $this->loginforpwd($param);
            }
            if($result['errcode'])
            {
                return $result;
            }
            else
            {
                $user_token = $result['data']['_user_token'];
            }
        }
        else
        {
            $user_token = $user->auth_token;
        }

        if(!$user_token)
        {
            //需要重新登录
            $this->setData("loginstatus",0);
            return $this->success();
        }
        else
        {
            //已登录
            $this->setData("loginstatus",1);
            $user_id = User::where("auth_token",$user_token)->first()->id;
            //生成重新向回调
            $callback = $res['callback'];
            $platform_openid = PlatformOpenid::where(["platform_id"=>$platform_id,"user_id"=>$user_id])->first();
            if(!$platform_openid)
            {
                $platform_openid = new PlatformOpenid();
                $platform_openid->platform_id = $platform_id;
                $platform_openid->user_id = $user_id;
                $platform_openid->openid = md5($platform_id."_".$user_id);
                $platform_openid->save();
            }
            $jumpurl = CurlInvoker::makeSecurityUrl($callback,["openid"=>$platform_openid->openid,"_user_token"=>$user_token],true,30,$platform_id);
            $this->setData("jumpurl",$jumpurl);
            return $this->success();
        }

    }

    /**
     * @name authlogin
     * @description  授权登录用的接口，以获取openid
     * @param $param
     * platform_id: 平台id
     *
     * login_type: 登录方式('smsLogin':'短信登录','pwdLogin':'密码登录')
     * mobile: 手机号码
     * verifycode: 验证码
     *
     * pwdLogin:
     * username: 手机号
     * password: 登录密码
     *
     * @return array
     * openid当前platform_id的唯一用户id
     */
    public function authlogin($param){

        $platform_id = $param['platform_id'];
        $platform = Helper::load_api_key($platform_id);
        if(!$platform)
        {
            return ['errcode'=>FanweErrcode::OPEN_PLATFORMID_ERROR,"message"=>"平台ID错误"];
        }
        if($param['login_type']=='smsLogin')
        {
            //短信登录
            $param['auth'] = 1;
            $result = $this->login($param);
        }
        else
        {
            //密码登录
            $param['auth'] = 1;
            $result = $this->loginforpwd($param);
        }
        if($result['errcode'])
        {
            return $result;
        }
        else
        {
            $user_token = $result['data']['_user_token'];

            //已登录
            $user_id = User::where("auth_token",$user_token)->first()->id;
            $platform_openid = PlatformOpenid::where(["platform_id"=>$platform_id,"user_id"=>$user_id])->first();
            if(!$platform_openid)
            {
                $platform_openid = new PlatformOpenid();
                $platform_openid->platform_id = $platform_id;
                $platform_openid->user_id = $user_id;
                $platform_openid->openid = md5($platform_id."_".$user_id);
                $platform_openid->save();
            }
            $this->setData("openid",$platform_openid->openid);
            return $this->success();
        }

    }


}