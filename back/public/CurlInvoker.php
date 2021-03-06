<?php

const API_KEYFILE = 'D:/wamp/www/fanwewallets/storage/apikey/1284fa71e890fea1.key';
const API_URL = 'http://open.fanwewallets.co';

class CryptAES
{

    protected $cipher = MCRYPT_RIJNDAEL_128;

    protected $mode = MCRYPT_MODE_ECB;

    protected $pad_method = NULL;

    protected $secret_key = '';

    protected $iv = '';



    public function set_cipher($cipher)

    {

        $this->cipher = $cipher;

    }



    public function set_mode($mode)

    {

        $this->mode = $mode;

    }



    public function set_iv($iv)

    {

        $this->iv = $iv;

    }



    public function set_key($key)

    {

        $this->secret_key = $key;

    }



    public function require_pkcs5()

    {

        $this->pad_method = 'pkcs5';

    }



    protected function pad_or_unpad($str, $ext)

    {

        if ( is_null($this->pad_method) )

        {

            return $str;

        }

        else

        {

            $func_name = __CLASS__ . '::' . $this->pad_method . '_' . $ext . 'pad';

            if ( is_callable($func_name) )

            {

                $size = mcrypt_get_block_size($this->cipher, $this->mode);

                return call_user_func($func_name, $str, $size);

            }

        }

        return $str;

    }



    protected function pad($str)

    {

        return $this->pad_or_unpad($str, '');

    }



    protected function unpad($str)

    {

        return $this->pad_or_unpad($str, 'un');

    }



    public function encrypt($str)

    {

        $str = $this->pad($str);

        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');



        if ( empty($this->iv) )

        {

            $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        }

        else

        {

            $iv = $this->iv;

        }



        mcrypt_generic_init($td, $this->secret_key, $iv);

        $cyper_text = mcrypt_generic($td, $str);

        $rt=base64_encode($cyper_text);

        //$rt = bin2hex($cyper_text);

        mcrypt_generic_deinit($td);

        mcrypt_module_close($td);



        return $rt;

    }



    public function decrypt($str){

        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');



        if ( empty($this->iv) )

        {

            $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        }

        else

        {

            $iv = $this->iv;

        }



        mcrypt_generic_init($td, $this->secret_key, $iv);

        //$decrypted_text = mdecrypt_generic($td, self::hex2bin($str));

        $decrypted_text = mdecrypt_generic($td, base64_decode($str));

        $rt = $decrypted_text;

        mcrypt_generic_deinit($td);

        mcrypt_module_close($td);



        return $this->unpad($rt);

    }

    /*

       public static function hex2bin($hexdata) {
           $bindata = '';
           $length = strlen($hexdata);
           for ($i=0; $i amp;< $length; $i += 2)
           {
               $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
           }
           return $bindata;

       }
   */

    public static function pkcs5_pad($text, $blocksize)

    {

        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);

    }


    public static function pkcs5_unpad($text)
    {

        $pad = ord($text{strlen($text) - 1});

        if ($pad > strlen($text)) return false;

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;

        return substr($text, 0, -1 * $pad);
    }
}
class CurlInvoker
{
    /**
     * 应用开发的platform_id
     */ 
    private $platform_id;
    
    /**
     * 应用开发的App密钥（用于API服务接口调用时的参数签名）
     */
    private $platform_secret;

    //单例
    private static $instance;
    
    /**
     * 构造函数
     * 
     * @param $platform_id 应用开发的platform_id
     * @param $platform_secret 应用开发的App密钥（用于API服务接口调用时的参数签名）
     * @return void
     */
    public function __construct()
    {
        $keyfile = API_KEYFILE;
        if (!file_exists($keyfile)) {
            return;
        }
        $keystr = file_get_contents(API_KEYFILE);
        $keyinfo = json_decode($keystr, true);
        $this->platform_id = array_key_exists('platform_id', $keyinfo) ? $keyinfo['platform_id'] : '';
        $this->platform_secret = array_key_exists('platform_secret', $keyinfo) ? $keyinfo['platform_secret'] : '';
    }

    
    /**
     * 调用各种SAAS系统HTTP请求方式的API服务接口，这些服务接口都遵循SAAS系统API服务接口规范，接口参数和签名都是按规范进行传递和验证。
     *
     * $service 结构 $group.$type/$class/$func
     * $group：com,app
     * $type：struct,data,service
     * $class：服务的类名
     * $func：方法
     *
     * $encrypt:是否用ase加密参数
     *
     * @param $args 具体的接口调用参数（除platform_id, timestamp, signature外），PHP数组对象，如：array("domain"=>"xxx.yydb.fanwe.com")
     * @return 接口调用结果，数组对象，如：array("errcode"=>0,"errmsg"=>"","data"=>array())
     */
    public static function invoke($service, $args, $encrypt = false, $timeoutMinutes = 30)
    {
        $url = API_URL."/".$service;
        if(self::$instance==null)
        {
            self::$instance = new CurlInvoker();
        }
        try {
            // 生成HTTP请求参数
            $params = self::makeRequestParameters($args);
            if($encrypt)
            {
                $_security_params = [];
                $_security_params['_security_params'] = self::encodeSecurityParams($params, $timeoutMinutes);
                $_security_params['_platform_id'] = self::$instance->platform_id;
                $params = $_security_params;
            }
            // 执行HTTP POST请求
            $ch = curl_init(); // 初始化curl
            curl_setopt($ch, CURLOPT_URL, $url); // 服务地址
            curl_setopt($ch, CURLOPT_HEADER, false); // 设置header
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 要求结果为字符串且输出到屏幕上
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // POST请求方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $data = curl_exec($ch); // 运行curl

            if(empty($data))
            {
            	$data = curl_error($ch);
            }
            curl_close($ch);
            // 将返回值转为PHP数组对象并返回
            $result = json_decode($data, true);
            return is_null($result) ? array("errcode"=>10000,"message"=>"请求出现异常") : $result;
        } catch (Exception $e) {
            return array("errcode"=>10000,"errmsg"=>"请求出现异常: ".$e->getMessage());
        }
    }
    
    /**
     * 生成SAAS系统API调用的请求参数，请求参数中将包含基本的参数（如：platform_id,timestamp,signature），并返回包含这些参数的数组数据。
     *
     * @param $args 具体的接口调用参数（除platform_id, timestamp, signature外），PHP数组对象，如：array("domain"=>"xxx.yydb.fanwe.com")
     * @return 已生成的参数数组，如：array("platform_id"=>"","timestamp"=>1457839056,"signature"=>"")
     */
    private static function makeRequestParameters($args)
    {
        // 计算参数签名，并设置返回值
        $systime = time();
        $params = array();
        $result = array();
        foreach($args as $key=>$value) {
            if ($key == 'platform_id' || $key == 'timestamp' || $key == 'signature') continue;
            $params[] = $key.$value;
            $result[$key] = $value;
        }
        $params['platform_id'] = 'platform_id'.self::$instance->platform_id;
        $params['timestamp'] = 'timestamp'.$systime;
        sort($params, SORT_STRING);
        $paramsStr = implode($params);
        $signature = md5(self::$instance->platform_secret.$paramsStr.self::$instance->platform_secret);
        $result['platform_id'] = self::$instance->platform_id;
        $result['timestamp'] = $systime;
        $result['signature'] = $signature;


        // 返回结果
        return $result;
    }


    //关于URL加密的处理
    /**
     * 生成方维系统间信息安全传递地址，将要传递的参数加密后附加到指定地址后面。
     * @param $url 原始地址，用于附加安全参数
     * @param $params 待附加的参数数组
     * @param $widthAppid 可选参数，生成的安全地址是否附带appid参数（参数名：_saas_appid），默认不附带
     * @param $timeoutMinutes 安全参数过期时间（单位：分钟），小于等于0表示永不过期
     * @return 附加安全参数后的安全地址
     */
    public static function makeSecurityUrl($url, $params, $withAppid = false, $timeoutMinutes = 0)
    {
        if(self::$instance==null)
        {
            self::$instance = new CurlInvoker();
        }

        // 将参数数组加密成安全参数字符串
        $encstr = self::encodeSecurityParams($params, $timeoutMinutes);
        // 将加密后的参数附加到$url后面，然后返回
        $split = strpos($url, '?') === false ? '?' : '&';
        $url .= $split.'_security_params='.urlencode($encstr);
        if ($withAppid) {
            $url .= '&_platform_id='.self::$instance->platform_id;
        }
        return $url;
    }

    /**
     * 加密指定的数组成生成方维系统间传递的安全参数字符串，以便通过HTTP的GET请求或POST请求进行传递
     * @param $params 待附加的参数数组
     * @param $timeoutMinutes 安全参数过期时间（单位：分钟），小于等于0表示永不过期
     * @return 加密后的安全参数字符串
     */
    private static function encodeSecurityParams($params, $timeoutMinutes = 0)
    {
        // 添加验证过期参数
        $params['_timestamp'] = time();
        if ($timeoutMinutes > 0) {
            $params['_timeout'] = $timeoutMinutes;
        }
        // 先将参数数组转为json格式字符串，然后加密
        $json = json_encode($params);
        $aes = new CryptAES();
        $aes->set_key(self::$instance->platform_secret);
        $aes->require_pkcs5();
        $encstr = $aes->encrypt($json);
        // 返回加密后的字符串
        return $encstr;
    }



    /**
     * 解密方维系统间传递的安全地址中的加密参数。如果参数已超时，那么返回false
     * @param $paramsStr 加密参数字符串
     * @return 解密后得到的原始安全参数数组，参数超时时返回false
     */
    public static function decodeSecurityParams($paramsStr)
    {
        if(self::$instance==null)
        {
            self::$instance = new CurlInvoker();
        }

        // 对加密的参数进行解密
        $aes = new CryptAES();
        $aes->set_key(self::$instance->platform_secret);
        $aes->require_pkcs5();
        $decstr = $aes->decrypt($paramsStr);
        // 将解密后的json字符串转成数组
        $ret = empty($decstr) ? array() : json_decode($decstr, true);
        // 验证参数是否过期
        $timestamp = array_key_exists('_timestamp', $ret) ? $ret['_timestamp'] : 0;
        $timeout = array_key_exists('_timeout', $ret) ? $ret['_timeout'] : 0;
        if ($timestamp <= 0) {
            return false;
        }
        if ($timeout > 0) {
            if (abs($timestamp - time()) > $timeout * 60) { // 误差超过指定时间，已超时
                return false;
            }
        }
        // 删除数组中的时间戳和超时设置参数
        if (array_key_exists('_timestamp', $ret)) {
            unset($ret['_timestamp']);
        }
        if (array_key_exists('_timeout', $ret)) {
            unset($ret['_timeout']);
        }
        // 返回结果
        return $ret;
    }

}
