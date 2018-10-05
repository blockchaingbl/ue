<?php

namespace App\Http\Models\Web;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Common\SmsCode;
use App\Http\Models\ServiceModule;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class User extends Base implements Authenticatable
{
    use AuthUser;
    protected $table = 'user';

    protected $fillable = [
        'username', 'mobile', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public $timestamps = false;


    /**
     * 注册账户
     * @param $data
     * @return array
     */
    public  static  function createUser($data){

        if($data['username']==""){
            return parent::error("请输入用户名","username");
        }

        if(self::getUserByUserName($data['username'])){
            return parent::error("用户名已存在","username");
        }

        if(!parent::checkNickname($data['username']))
        {
            return parent::error("用户名只允许中文、大小写字母、数字，最少2个，最多8个字符","username");
        }

//        if($data['email']==""){
//            return parent::error("请输入邮箱地址","email");
//        }

//        if(!parent::checkEmail($data['email'])){
//            return parent::error("邮箱地址格式不正确","email");
//        }
//
//        if(self::getUserByEmail($data['email'])){
//            return parent::error("邮箱已被注册","email");
//        }


        if(self::getUserByMobile($data['mobile'])){
            return parent::error("手机已被注册","mobile");
        }

        if($data['password']==""){
            return parent::error("请输入密码","password");
        }

        if(!parent::checkPassword($data['password'])){
            return parent::error("密码格式不能少于6位，大于16位","password");
        }

//        if(!parent::checkPwdSafe($data['password'])){
//            return parent::error("为了提高安全性，密码请使用 字母+数字 的组合","password");
//        }

        if($data['pid']&&!self::getUser($data['pid'])){
            return parent::error("推荐人不存在","pid");
        }

        $user_data = [];
        $user_data['username'] = $data['username'];


        if($data['pid'])
            $user_data['pid'] = $data['pid'];

        //客户端IP、设备、来源信息
        $user_data['client_ip'] = Helper::get_client_ip();
        $user_data['device'] = Helper::getDevice();
        if(Helper::isApp())
        {
            $user_data['source'] = 'app';
        }
        else
        {
            $user_data['source'] = 'browser';
        }

        $user_data['password'] = bcrypt($data['password']);
        $user_data['password_level'] = parent::password_level($data['password']);
        $user_data['create_time'] = date("Y-m-d H:i:s",time());
        $user_data['mobile'] = $data['mobile'];
        $user_data['status'] = 1;
        $user_data['mobile_code'] = $data['mobile_code'];

        $id = User::insertGetId($user_data);

        if($id > 0){

            //生成邀请码
            do{
                try{
                    $num = 6;
                    if($id>500000)
                        $num = 8;
                    $invite_code = Helper::createInviteCode($num,$id);
                    User::where("id",$id)->update(["invite_code"=>$invite_code]);
                }catch(\Exception $e)
                {
                    Log::warn($e->getMessage());
                }
            }while($e);

            return parent::success("注册成功",$user_data);
        }
        else{
            return parent::success("注册失败");
        }
    }

    /**
     * 修改密码
     * @param $data array("id"=>?,"password"=?)
     * @return array
     */
    public static function  reSetPassword($data)
    {
        if(!self::getUser($data['id'])){
            return parent::error("会员不存在","id",FanweErrcode::USER_NOT_EXSITS);
        }

        if($data['password']==""){
            return parent::error("请输入密码","password");
        }

        if(!parent::checkPassword($data['password'])){
            return parent::error("密码格式不能少于6位,大于16位","password");
        }

        User::where(['id'=>$data['id']])->update(["password"=>bcrypt($data['password']),"password_level"=>parent::password_level($data['password'])]);

        return parent::success("重置成功");
    }

    public static function reSetMobile($data){

        if($data['verify_code']==""){
            return parent::error("请输入验证码","verify_code");
        }

        if(!SmsCode::checkResetCode($data['mobile'],$data['verify_code'])){
            return parent::error("验证码错误或已失效","verify_code");
        }

       $user = self::getUser($data['id']);
        if(!$user){
            return parent::error("会员不存在","id",FanweErrcode::USER_NOT_EXSITS);
        }
        if($user->mobile!=""){
            if($data['oldmobile']==""){
                return parent::error("请输入旧手机号码","oldmobile");
            }
            if($user->mobile!=$data['oldmobile']){
                return parent::error("会员旧手机号码错误","oldmobile");
            }
        }

        if($data['mobile']==$data['oldmobile']){
            return parent::error("新手机号码不能跟旧手机号码一样","mobile");
        }

        if(!parent::checkMobile( $data['mobile'])){
            return parent::error("手机号码格式错误","mobile");
        }



        if(self::getUserByMobile($data['mobile'])){
            return parent::error("手机号码已被使用","mobile");
        }

        $aff = User::where(['id'=>$data['id']])->update(["mobile"=>$data['mobile']]);

        if($aff){
            SmsCode::clearCode("getpassword",$data['mobile']);
            return parent::success("绑定成功");
        }
        else{
            return parent::error("绑定失败");
        }
    }

    /**
     * 设置账户信息
     * @param $data
     * @return array
     */
    public static function setUserProfile($data){
        if((int)$data['user_id']==0 || !self::getUser($data['user_id'])){
            return parent::error("会员不存在",array(),FanweErrcode::USER_NOT_EXSITS);
        }
        return parent::success("设置成功",UserProfile::updateUserProfile($data));
    }

    /**获得会员资料
     * @param $data
     * @return array
     */
    public static function getUserProfile($data){
        if((int)$data['user_id']==0){
            return parent::error("会员不存在");
        }
        return parent::success("获得成功",UserProfile::getUserProfile($data));
    }

    /**
     * 获得会员信息
     * @param $id
     * @return mixed
     */
    public  static  function getUser($id)
    {
        return User::where(["id"=>$id])->first();
    }

    /**会员ID
     * @param $mobile
     * @return mixed
     */
    public  static  function getUserByMobile($mobile)
    {
        return User::where(["mobile"=>$mobile])->value("id");
    }

    /**会员ID
     * @param $mobile
     * @return mixed
     */
    public  static  function getUserByUserName($username)
    {
        return User::where(["username"=>$username])->value("id");
    }

    /**会员ID
     * @param $mobile
     * @return mixed
     */
    public  static  function getUserByAddress($address)
    {
        return User::where(["address"=>$address])->value("id");
    }

    /**会员ID
     * @param $mobile
     * @return mixed
     */
    public  static  function getUserByEmail($email)
    {
        return User::where(["email"=>$email])->value("id");
    }

    /**账户手机
     * @param $id
     * @return mixed
     */
    public static function getMobileById($id){
        return User::where(["id"=>$id])->value("mobile");
    }

    /**
     * 通过邀请码获取会员ID
     * @param $mobile
     * @return mixed
     */
    public static function getUserByInviteCode($invite_code)
    {
        return User::where(["invite_code"=>$invite_code])->value("id");
    }

    /**
     * 通过用户名获取会员资料
     * @param $mobile
     * @return array
     */
    public  static  function getDatabyName($name)
    {
        return User::where(["username"=>$name])->first();
    }




    /**检查登陆
     * @param $data
     * @return array
     */
    public static function checklogin($data){

        if($data['username']==""){
            return parent::error("请输入登录账号","username");
        }

        if($data['password']==""){
            return parent::error("请输入登录密码","password");
        }

        $rs =  User::where("username","=",$data['username'])->orWhere("email","=",$data['username'])->first();

        if($rs){

            if($rs->status==0){
                return parent::error("账号不可用","username");
            }

            return parent::success("检测成功",$rs);

        }
        else{
            return parent::error("账号不存在","username");
        }
    }

    /**
     * 根据手机重置密码
     * @param $data
     * @return array
     */
    public static function resetPasswordByMobile($data){

        if( $data['mobile']==""){
            return parent::error("请输入手机号码","mobile");
        }

        if(!parent::checkMobile( $data['mobile'])){
            return parent::error("手机号码格式错误","mobile",FanweErrcode::MOBILE_FROMAT_ERROR);
        }

        if($data['verify_code']==""){
            return parent::error("请输入验证码","verify_code");
        }
        if(!SmsCode::checkResetCode($data['mobile'],$data['verify_code'])){
            return parent::error("验证码错误或已失效","verify_code");
        }

        if($data['password']==""){
            return parent::error("请输入密码","password");
        }

        if(!parent::checkPassword($data['password'])){
            return parent::error("密码格式不能少于6位,大于16位","password",FanweErrcode::PASSWORD_FORMAT_ERROR);
        }


        if(!self::getUserByMobile($data['mobile'])){
            return parent::error("账号不存在","mobile",FanweErrcode::USER_NOT_EXSITS);
        }

        User::where(['mobile'=>$data['mobile']])->update(["password"=>bcrypt($data['password']),"password_level"=>parent::password_level($data['password'])]);
        SmsCode::clearCode("getpassword",$data['mobile']);
        return parent::success("重置成功");
    }

    /**
     * 根据邮箱重置密码
     * @param $data
     * @return array
     */
    public static function resetPasswordByEmail($data){

        if($data['email']==""){
            return parent::error("请输入邮箱地址","email");
        }

        if(!parent::checkEmail($data['email'])){
            return parent::error("邮箱地址格式错误","email");
        }

        if($data['checkcode']==""){
            return parent::error("请输入验证码","checkcode");
        }

        $code = Cache::get("email_code:".$data['email']);
        if($code != $data['checkcode']){
            return parent::error("验证错误或已失效，请重新发送邮件","checkcode");
        }

        if($data['password']==""){
            return parent::error("请输入密码","password");
        }

        if(!parent::checkPassword($data['password'])){
            return parent::error("密码格式不能少于6位,大于16位","password",FanweErrcode::PASSWORD_FORMAT_ERROR);
        }

        if(!self::getUserByEmail($data['email'])){
            return parent::error("账号不存在","email");
        }

        User::where(['email'=>$data['email']])->update(["password"=>bcrypt($data['password']),"password_level"=>parent::password_level($data['password'])]);
        Cache::forget("email_code:".$data['email']);
        return parent::success("重置成功");
    }

    /**
     * 更新
     */
    public static function updateInfo($data){

        $rs=User::where(['id'=>$data['id']])->update($data);
        if($rs>0){
            return parent::success("更新成功");
        }else{
            return parent::success("更新失败");
        }
    }



    public static function filterEmoji($string){
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$string);
    }

    public function parent_user(){
        return $this->belongsTo('App\Http\Models\Web\User','pid')
            ->select(array('username','id','avatar','mobile'));
    }

    public function attached_user(){
        return $this->belongsTo('App\Http\Models\Web\User','attached')
            ->select(array('username','id','avatar','mobile'));
    }



}