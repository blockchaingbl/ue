<?php
// +----------------------------------------------------------------------
// | Fanwe 方维系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

namespace App\Http\Controllers\Web;


use App\FanweErrcode;
use Helper;
use App\Http\Models\Common\SmsCode;
use App\Http\Models\Common\SmsList;
use App\Http\Models\Web\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Validator;
use Session;

class ToolController extends BaseController
{
    /*
     * 获取短信验证码
     */
    public function smscode(Request $request){

        if($request->mobile==""){
            $data['message'] = '请输入手机号码';
            $data['errcode'] = FanweErrcode::SYSTEM_ERROR;
            return $data;
        }

        if(!Base::checkMobile($request->mobile)){
            $data['message'] = '手机号码格式错误';
            $data['errcode'] = FanweErrcode::SYSTEM_ERROR;
            return $data;
        }

        $session_id = Session::getId();
        $sec = SmsList::getGuard($request->mobile,$session_id);

        //没有锁
        if($sec==0) {
            if($request->input("sendtype")=="register"){
                $save_code = SmsCode::genRegisterCode($request->mobile);
            }
            elseif($request->input("sendtype")=="reset"){
                $save_code = SmsCode::genResetCode($request->mobile);
            }

            if($save_code != ""){
                if(config("app.debug")) {
                    $message = '验证码是：' . $save_code;
                }
                else{
                    $result = SmsList::sendSms($request->mobile,"您的验证码为".$save_code."，请妥善保管",2);
                    if($result['status']==1){
                        $message = '验证码已发送';
                    }
                    else{
                        return $this->error($result['msg'],1);
                    }
                }
                SmsList::setGuard($request->mobile,$session_id);
                return $this->success($message,1);
            }else{
                return $this->error("验证码发送失败",1);
            }
        }
        else{
            return $this->error("短信发送太频繁，还剩".$sec."秒",1);
        }

    }

    /**
     * 短信队列
     * @param Request $request
     * @return mixed
     */
    public function smsQueue(Request $request)
    {
        if($request->input("auth")=="fanwesmsqueue")
        {
            $sms = SmsList::popSms();
            $result = SmsList::sendSms($sms['mobile'],$sms['content'],$sms['is_adv']);
            if(intval($result['status'])==1)
            {
                return $this->success();
            }
            else
            {
                return $this->error($result['msg']);
            }
        }
        else
        {
            $result['errcode'] = -1;
        }
        return $result;
    }

   public function upload(Request $request){
        $upload_file = $request->file("file");
        $keyId = intval($request->input("keyId"));
        $key = $request->input("key");

        //验证最大上传大小限制
         $maxSize = config('app.file_maxsize')*1024*1024;

        if($upload_file->getSize() >= $maxSize) {
            $return['errcode'] = 10000;
            $return['message'] = '文件大小超出最大限制';
            return $return;
        }

        $fileFolder = $key."/".$keyId;

        $fileName = strtolower($keyId).".".$upload_file->getClientOriginalExtension();

        $file = [
            'fileFolder'=>$fileFolder, // 上传到OSS的指定文件夹
            'fileName'=>$fileName, // 上传文件名（xxx.jpg）
            'filePath'=>$upload_file->getPath()."/".$upload_file->getFilename() // 上传文件对象
        ];

        $res = Helper::upload_to_oss($file);

        if($res){
                $return['errcode'] = 0;
                $return['message'] = '上传成功';
                $return['data'] = ['src'=>$res['src'],"width"=>$res['width'],'height'=>$res['height']];
                return $return;
         }
        else{
            $return['errcode'] = 10000;
            $return['message'] = '上传失败';
            return $return;
        }
    }

    /*
     * 获取邮箱验证码
     */
    public function emailcode(Request $request)
    {
        $email = $request->input("email");
        if(!$email){
            return $this->error("请输入邮箱地址","",1);
        }
        if(!Base::checkEmail($email)){
            return $this->error("邮箱地址格式不正确","",1);
        }

        $email_expire = config("app.email_expire"); //验证码有效时间
        $email_guard = config("app.email_guard"); //邮件发送频率

        $codeLock = Cache::get("email_lock:".$email);
        if($codeLock)
        {
            return $this->error("操作太频繁，请稍后再试","",1);
        }
        else
        {
            try {

                $code = rand(100000,999999);

                $mail = new Message();

//                $mail->setFrom('名人堂 <'.config("app.email_username").'>')
//                    ->addTo($email)
//                    ->setSubject('名人堂-密码找回')
//                    ->setBody("立即前往找回密码：".url("forget?email=".$email."&code=".$code));

                $mail->setFrom('名人堂 <'.config("app.email_username").'>')
                    ->addTo($email)
                    ->setSubject('找回密码服务')
                    ->setHTMLBody('<div style="position:relative;width:100%;height:360px;background:#dddddd;"><div style="position:absolute;left:0;right:0;top:80px;width:650px;height:200px;margin:0 auto;background:#ffffff;"><p style="margin:50px 0 0 50px;">尊敬的用户：</p><p style="margin:10px 0 0 50px;">您好！</p><p style="margin:10px 0 0 50px;">您正在找回密码，<a href="'.url("forget?email=".$email."&code=".$code).'">立即前往</a> 继续操作。</p></div></div>');

                $mailer = new SmtpMailer([
                    'host' => config("app.email_host"),
                    'username' => config("app.email_username"),
                    'password' => config("app.email_password"),
                    'secure' => config("app.email_secure")
                ]);

                //发送邮件
                $mailer->send($mail);

                //记录验证码
                Cache::put("email_code:".$email,$code,$email_expire/60);

                if($email_guard>0)
                {
                    //发送频率大于0，设置锁
                    $now_date = date("Y-m-d H:i:s",time());
                    Cache::put("email_lock:".$email,$now_date,$email_guard/60);
                }

                $data["guard_time"] = config("app.email_guard");
                return $this->success("已发送验证码到邮箱",$data,1);
            } catch (\Exception $e) {
//                return $this->error($e->getMessage(),"",1);
                return $this->error("邮件发送失败","",1);
            }
        }
    }

}