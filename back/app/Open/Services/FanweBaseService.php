<?php

namespace App\Open\Services;



use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Web\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

abstract class FanweBaseService
{
    //服务返回的主体
    private $errcode;
    private $message;

    //每个业务交互的数据包 Array，非stdClass
    protected $data;
    protected $platform;

    public $log;

    /**
     * 设置数据
     * @param $key
     * @param $value
     */
    protected function setData($key,$value)
    {
        //强转为数组
        $this->data[$key] = json_decode(json_encode($value),true);
    }

    //成功
    protected function success($message='操作成功')
    {
        $this->message = $message;
        $this->errcode = FanweErrcode::SYSTEM_SUCCESS;
        return $this->out();
    }

    //失败，数据操作错误，执行中断，如有事务完全回滚
    protected function error($message='操作失败',$code = FanweErrcode::SYSTEM_ERROR)
    {
        $this->message  = $message;
        $this->errcode = $code;
        return $this->out();
    }

    //返回数组
    protected function out()
    {
        return ['data'=>$this->data,
            'errcode'=>$this->errcode,
            'message'=>$this->message,
            'front_version'=>config("app.front_version"),
            "update_message"=>config("app.front_update_message"),
            'init_version'=>db_config('INIT_VERSION')
        ];
    }


    //初始化平台
    public function init($param)
    {
        if(config("app.api_crypto")) //需要签名校验
        {
            $platform = Helper::load_api_key($param['platform_id']);
            if(!$platform)
            {
                return ['errcode'=>FanweErrcode::OPEN_PLATFORMID_ERROR,"message"=>"平台ID错误"];
            }
            $this->platform = $platform;
        }
        return ['errcode'=>FanweErrcode::SYSTEM_SUCCESS,"message"=>"初始化成功"];

    }


    protected function init_user($param)
    {
        $token = $param['_user_token'];
        $user = User::where("auth_token",$token)->first();
        if(!$user)
        {
            return $this->error("授权无效",FanweErrcode::AUTH_INVALID);
        }
        else
        {
            if($user->auth_expire<time())
            {
                return $this->error("授权过期",FanweErrcode::AUTH_INVALID);
            }

            if(!$user->status)
            {
                return $this->error("账户被禁用",FanweErrcode::USER_IS_FORBID);
            }
        }

        //续租
        $lifetime = config("app.auth_time");
        $exipre = time() + intval($lifetime)*60;
        $user->auth_expire = $exipre;
        $user->save();
        return $user;
    }

	
}
