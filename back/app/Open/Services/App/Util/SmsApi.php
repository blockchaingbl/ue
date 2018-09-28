<?php
namespace App\Open\Services\App\Util;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Common\SmsCode;
use App\Http\Models\Common\SmsList;
use App\Http\Models\Web\Base;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\Log;


class SmsApi extends FanweBaseService
{

    /**
     * @name send
     * @description  短信验证码发送
     * @param
     * mobile:接收验证码的手机号
     * type: 短信验证类型 0:注册/登录
     * @return
     * second：剩余的下次发送的秒数
     */
    public function send($param)
    {

        if(!config("app.debug")&&!Helper::isApp())
        {
            return $this->error("请使用APP访问");
        }

        $mobile = $param['mobile'];
        $type = intval($param['type']);
        $mobile_code = $param['mobile_code'];

        if($mobile==""){
            return $this->error("请输入手机号码",FanweErrcode::MOBILE_EMPTY);
        }
        if(!Base::checkMobile($mobile)){
            return $this->error("手机号码格式错误",FanweErrcode::MOBILE_FROMAT_ERROR);
        }
        $sec = SmsList::getGuard($mobile);
        $this->setData("second",$sec);

        //没有锁
        if($sec==0) {

            if($type==0){
                $save_code = SmsCode::genRegisterCode($mobile);
            }

            if($save_code != ""){
                if(config("app.debug")) {
                    $message = '验证码是：' . $save_code;
                }
                else{
                    $result = SmsList::sendSms($mobile,"您的验证码为".$save_code."，请妥善保管",2,$mobile_code);
                    if($result['status']==1){
                        $message = '验证码已发送';
                    }
                    else{
                        Log::warn(json_encode($result));
                        return $this->error($result['msg'],FanweErrcode::SYSTEM_ERROR);
                    }
                }
                SmsList::setGuard($mobile);
                $sec = SmsList::getGuard($mobile);
                $this->setData("second",$sec);
                return $this->success($message);
            }else{
                return $this->error("验证码发送失败",FanweErrcode::SYSTEM_ERROR);
            }
        }
        else{
            return $this->error("短信发送太频繁，还剩".$sec."秒",FanweErrcode::SYSTEM_ERROR);
        }
    }

}