<?php
namespace App\Library;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class Weixin
{
	//微信APPID
   	var $app_id="";
    //微信秘钥
    var $app_secret="";
    //跳转链接
    var $redirect_url="";
    //传递的方式
    var $scope="";
    var $state=1;
    //用户同意授权，获取code
    var $code="";
    //授权通过后获取access_token  openid
    var $access_token="";
    var $openid="";
    var $is_platform = 0;
    var $component_appid;
    var $component_access_token;
    var $platform;
    function __construct($app_id="",$app_secret="",$redirect_url="",$scope="snsapi_userinfo",$state=1,$is_platform = 0)
    {
        $this->app_id=$app_id;
        $this->app_secret=$app_secret;
        $this->redirect_url=urlencode($redirect_url);
        $this->scope=$scope;
        $this->state=$state;
        $this->is_platform = $is_platform;
        if($this->is_platform){
            $weixin_conf = Cache::get("weixin_conf");
            if($weixin_conf==null){
                $tweixin_conf = DB::table("weixin_conf")->get();
                $tweixin_conf = json_decode(json_encode($tweixin_conf),1);
                $weixin_conf  = "";
                foreach($tweixin_conf as $k=>$v){
                    $weixin_conf[$v['name']]=$v['value'];
                }

                Cache::put("weixin_conf",json_encode($weixin_conf),100);
            }
            else{
                $weixin_conf = json_decode($weixin_conf,1);
            }

            $this->component_appid = $weixin_conf['platform_appid'];
            $this->component_access_token =  $weixin_conf['platform_component_access_token'];
            $this->option = array(
                'platform_token'=>$weixin_conf['platform_token'], //填写你设定的token
                'platform_encodingAesKey'=>$weixin_conf['platform_encodingAesKey'], //填写加密用的EncodingAESKey
                'platform_appid'=>$weixin_conf['platform_appid'], //填写高级调用功能的app id
                'platform_appsecret'=>$weixin_conf['platform_appsecret'], //填写高级调用功能的密钥

                'platform_component_verify_ticket'=>$weixin_conf['platform_component_verify_ticket'], //第三方通知
                'platform_component_access_token'=>$weixin_conf['platform_component_access_token'], //第三方平台令牌
                'platform_pre_auth_code'=>$weixin_conf['platform_pre_auth_code'], //第三方平台预授权码

                'platform_component_access_token_expire'=>$weixin_conf['platform_component_access_token_expire'],
                'platform_pre_auth_code_expire'=>$weixin_conf['platform_pre_auth_code_expire'],

                'logcallback'=>'log_result',
                'debug'=>true,
            );

            $this->option = array(
                'platform_token'=>$weixin_conf['platform_token'], //填写你设定的token
                'platform_encodingAesKey'=>$weixin_conf['platform_encodingAesKey'], //填写加密用的EncodingAESKey
                'platform_appid'=>$weixin_conf['platform_appid'], //填写高级调用功能的app id
                'platform_appsecret'=>$weixin_conf['platform_appsecret'], //填写高级调用功能的密钥

                'platform_component_verify_ticket'=>$weixin_conf['platform_component_verify_ticket'], //第三方通知
                'platform_component_access_token'=>$weixin_conf['platform_component_access_token'], //第三方平台令牌
                'platform_pre_auth_code'=>$weixin_conf['platform_pre_auth_code'], //第三方平台预授权码

                'platform_component_access_token_expire'=>$weixin_conf['platform_component_access_token_expire'],
                'platform_pre_auth_code_expire'=>$weixin_conf['platform_pre_auth_code_expire'],



                'logcallback'=>'log_result',
                'debug'=>true,
            );

            $account = DB::table("weixin_account")->where(["authorizer_appid"=>$this->app_id])->first();
            $account = json_decode(json_encode($account),1);
            if($account){
                $option_account=array(
                    'authorizer_access_token'=>$account['authorizer_access_token'],
                    'authorizer_access_token_expire'=>$account['expires_in'],
                    'authorizer_appid'=>$account['authorizer_appid'],
                    'authorizer_refresh_token'=>$account['authorizer_refresh_token'],
                );
                $this->option=array_merge($this->option,$option_account);
            }

            require_once base_path("app/Library/Wechat/platform_wechat.class.php");
            $this->platform = new \PlatformWechat($this->option);
            $new_token = $this->platform->check_platform_access_token();
            if($new_token!=$this->component_access_token){
                $this->component_access_token = $new_token;
            }
        }
    }
    public function scope_get_code(){
        if($this->is_platform){
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->app_id . "&redirect_uri=" . $this->redirect_url . "&response_type=code&scope=" . $this->scope . "&state=" . $this->state . "&component_appid=".$this->component_appid."#wechat_redirect";
        }else {
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->app_id . "&redirect_uri=" . $this->redirect_url . "&response_type=code&scope=" . $this->scope . "&state=" . $this->state . "#wechat_redirect";
        }
    	 return $url;
    }

    /**
     * 获得基础信息
     * @param $code
     * @return array|bool|mixed  {
                                    "openid": "oLVPpjqs9BhvzwPj5A-vTYAX3GLc",
                                    "nickname": "方倍",
                                    "sex": 1,
                                    "language": "zh_CN",
                                    "city": "Shenzhen",
                                    "province": "Guangdong",
                                    "country": "CN",
                                    "headimgurl": "http://wx.qlogo.cn/mmopen/utpKYf69VAbCRDRlbUsPsdQN38DoibCkrU6SAMCSNx558eTaLVM8PyM6jlEGzOrH67hyZibIZPXu4BK1XNWzSXB3Cs4qpBBg18/0",
                                    "privilege": []
                                   }
     */
    public function scope_get_userinfo($code){
    	$this->code=$code;
        if($this->is_platform){
            $get_token_url="https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$this->app_id."&code=".$this->code."&grant_type=authorization_code&component_appid=".$this->component_appid."&component_access_token=".$this->component_access_token;
            $token_info=$this->https_request($get_token_url);

            $token_info=json_decode($token_info['body'],true);

            if($token_info['errcode']>0)
            {
                return $token_info;
            }
            //$this->access_token=$token_info['access_token'];
            $this->openid=$token_info['openid'];

            $user_info = $this->platform->getOauthUserinfo($token_info['access_token'],$this->openid);

            return $user_info;
        }
        else{
    	    $get_token_url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->app_id."&secret=".$this->app_secret."&code=".$this->code."&grant_type=authorization_code";
		 
            $token_info=$this->https_request($get_token_url);

            $token_info=json_decode($token_info['body'],true);
            if($token_info['errcode']>0)
            {
                return $token_info;
            }
            $this->access_token=$token_info['access_token'];
            $this->openid=$token_info['openid'];
            $get_userinfo="https://api.weixin.qq.com/sns/userinfo?access_token=".$this->access_token."&openid=".$this->openid."&lang=zh_CN";
            $user_info=$this->https_request($get_userinfo);
            $user_info=json_decode($user_info['body'],true);

            return $user_info;
        }
    }

    public function scope_get_openid($code)
    {
        if($this->is_platform)
        {
            $this->code=$code;
            $get_token_url="https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$this->app_id."&code=".$this->code."&grant_type=authorization_code&component_appid=".$this->component_appid."&component_access_token=".$this->component_access_token;
            $token_info=$this->https_request($get_token_url);


            $token_info=json_decode($token_info['body'],true);

        }
        else
        {
            $this->code=$code;
            $get_token_url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->app_id."&secret=".$this->app_secret."&code=".$this->code."&grant_type=authorization_code";

            $token_info=$this->https_request($get_token_url);

            $token_info=json_decode($token_info['body'],true);
        }

        return $token_info;
    }

    
    public function https_request($url){
    	$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
		$http_response = curl_exec($curl);
		 if (curl_errno($curl) != 0)
        {
            return false;
        }

        $separator = '/\r\n\r\n|\n\n|\r\r/';
        list($http_header, $http_body) = preg_split($separator, $http_response, 2);

        $http_response = array('header' => $http_header,//肯定有值
                               'body'   => $http_body); //可能为空
		curl_close($curl);
		return $http_response;
    }


    public function get_platform_access_token()
    {
        return $this->platform->check_platform_authorizer_token();
    }

    public function get_access_token($refresh = false){

       $key = "weixin_access_token:".$this->app_id;

       $access_token = Redis::get($key);
       if($access_token ==null||$refresh){

            $get_token_url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->app_id&secret=$this->app_secret";
            $token_info=$this->https_request($get_token_url);
            $token_info=json_decode($token_info['body'],true);
            Redis::set($key,$token_info['access_token']);
            Redis::expire($key,$token_info['expires_in']);
           return  $token_info['access_token'];

        }
        else{
            return $access_token;
        }

    }

    /**
     * 获得完整的用户信息
     * @param $openid
     * @return array|bool|mixed {
                                    "subscribe": 1,
                                    "openid": "oLVPpjqs2BhvzwPj5A-vTYAX4GLc",
                                    "nickname": "方倍",
                                    "sex": 1,
                                    "language": "zh_CN",
                                    "city": "深圳",
                                    "province": "广东",
                                    "country": "中国",
                                    "headimgurl": "http://wx.qlogo.cn/mmopen/JcDicrZBlREhnNXZRudod9PmibRkIs5K2f1tUQ7lFjC63pYHaXGxNDgMzjGDEuvzYZbFOqtUXaxSdoZG6iane5ko9H30krIbzGv/0",
                                    "subscribe_time": 1386160805
                                 }
     */
    public function getSubscribeInfo($openid){

        $access_token = $this->get_access_token();

        $get_userinfo="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $user_info=$this->https_request($get_userinfo);
        $user_info=json_decode($user_info['body'],true);
        if($user_info['errcode']==40001)
        {
            $access_token = $this->get_access_token(true);
            $get_userinfo="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
            $user_info=$this->https_request($get_userinfo);
            $user_info=json_decode($user_info['body'],true);
        }

        return $user_info;

    }

    /**
     * 获得二维码
     * @param int $type 0 临时二维码 1永久二维码
     */
    public function getQrcode($type=0){
        $param = "";
        if($type==0){
            $param['action_name'] = "QR_SCENE";
            $param['action_info']['scene']['scene_str'] = Session::getId();
        }
        else{
            $param['action_name'] = "QR_LIMIT_SCENE";
            $param['action_info']['scene']['scene_id'] = rand(1,100000);
        }

        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;

        $data_string = json_encode($param);


        $rs = $this->http_request_json($url,$data_string);

        $rs = json_decode($rs,1);
        return $rs['ticket'];
    }

    private function http_request_json($url,$json){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
        );


        $result = curl_exec($ch);

        return $result;
    }

    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 创建带参数的临时二维码
     * @param $str
     * @return mixed
     */
    public function createParamsQrcode($key){
        $param = "";
        $param['expire_seconds'] = "2592000";
        $param['action_name'] = "QR_SCENE";
        $param['action_info']['scene']['scene_id'] = $key;

        $access_token = $this->get_platform_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $data_string = json_encode($param);

        $rs = $this->http_request_json($url,$data_string);
        $error_text = json_encode($rs);
        $rs = json_decode($rs,1);

//        if($rs['errcode']==40001)
//        {
//            $access_token = $this->get_access_token(true);
//            $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
//            $data_string = json_encode($param);
//
//            $rs = $this->http_request_json($url,$data_string);
//            $error_text = json_encode($rs);
//            $rs = json_decode($rs,1);
//        }

        if($rs['ticket']) {
            $qrcode = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $rs['ticket'];
        }else{
            echo "<script>alert(".$error_text.");</script>";
        }

        return $qrcode;
    }


    public function createLoginQrcode($key){
        $param = "";
        $param['expire_seconds'] = "2592000";
        $param['action_name'] = "QR_SCENE";
        $param['action_info']['scene']['scene_id'] = $key;

        $access_token = $this->get_platform_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $data_string = json_encode($param);

        $rs = $this->http_request_json($url,$data_string);
        $error_text = json_encode($rs);
        $rs = json_decode($rs,1);

//        if($rs['errcode']==40001)
//        {
//            $access_token = $this->get_access_token(true);
//            $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
//            $data_string = json_encode($param);
//
//            $rs = $this->http_request_json($url,$data_string);
//            $error_text = json_encode($rs);
//            $rs = json_decode($rs,1);
//        }

        return $rs;
    }

}

?>