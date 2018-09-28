<?php

namespace App\Open\Controllers\Open;

use App\FanweErrcode;
use App\Helper;
use App\Open\Library\CryptAES;
use App\Open\Services\ServiceInvoker;
use App\Open\Controllers\Controller as BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache,DB,Config;


class IndexController extends BaseController
{
    private $platform_id = null;
    private $platform_secret = null;

    //
    public function index(Request $request,$group,$type,$class,$func)
    {
        if($group=="system"&&$type=="date"&&$class=="time"&&$func=="stamp")
        {
            return ["timestamp"=>time()];
        }
        $param = $request->all();
        if($param['_security_params'])
        {
            $_security_params = $param['_security_params'];
            $platform = Helper::load_api_key($param['_platform_id']);
            if(!$platform)
            {
                return ['errcode'=>FanweErrcode::OPEN_PLATFORMID_ERROR,"message"=>"平台ID错误"];
            }
            $param = $this->decodeSecurityParams($_security_params,$platform->platform_secret);
        }

//        Config::set("app.api_crypto",false);
        if(config("app.api_crypto")) //需要签名校验
        {
            $platform = Helper::load_api_key($param['platform_id']);
            if(!$platform)
            {
                return ['errcode'=>FanweErrcode::OPEN_PLATFORMID_ERROR,"message"=>"平台ID错误"];
            }

            $this->platform_id = $platform->platform_id;
            $this->platform_secret = $platform->platform_secret;

            $result = $this->verifyRequestParameters($param);
            if($result['errcode']>0)
            {
                return $result;
            }
        }

        $service = $group.".".$type;
        if($class)
        {
            $service.="/".$class;
            if($func)
                $service.="/".$func;
        }

        $result = ServiceInvoker::invoke($service,$param);
        if($result)
            return $result;
        else
            return ['errcode'=>FanweErrcode::SYSTEM_ERROR,"message"=>"接口错误"];
    }


    /**
     * 验证SAAS系统API请求参数是否有效，这些请求参数必须按照SAAS系统API服务接口参数和签名规范进行传递。
     *
     *
     * @param $param HTTP请求参数数组
     * @return 验证结果，数组对象，如：array("errcode"=>0,"message"=>"")，验证通过时，errcode为0。
     */
    private function verifyRequestParameters($param)
    {
        // 获取HTTP请求参数并进行有效性判断
        $platform_id = array_key_exists('platform_id', $param) ? trim($param['platform_id']) : '';
        $timestamp = array_key_exists('timestamp', $param) ? trim($param['timestamp']) : '';
        $signature = array_key_exists('signature', $param) ? trim($param['signature']) : '';
        if (empty($platform_id) || empty($timestamp) || empty($signature) || !is_numeric($timestamp)) {
            return array('errcode'=>FanweErrcode::OPEN_INVALID_ARGUMENTS,'message'=>'参数格式错误');
        }

        // 计算参数签名
        $signParams = array();
        foreach($param as $key=>$value) {
            if ($key == 'signature') continue;
            $signParams[] = $key.$value;
        }
        sort($signParams, SORT_STRING);
        $signParamsStr = implode($signParams);
        $calcSignature = md5($this->platform_secret.$signParamsStr.$this->platform_secret);
        // 验证参数签名
        if (strtolower($signature) != strtolower($calcSignature)) {
            return array('errcode'=>FanweErrcode::OPEN_INVALID_SIGNATURE,'message'=>'签名错误');
        }
        // 验证时间戳
        $timestamp = intval($timestamp);
        if (abs($timestamp - time()) > 30) { // 网络延时误差在30秒之内
            return array('errcode'=>FanweErrcode::OPEN_SIGNATURE_EXPIRE,'message'=>'签名超时');
        }
        // 一切顺利，返回成功结果
        return array('errcode'=>0,'message'=>'');
    }


    /**
     * 解密AES加密的参数
     * @param $paramsStr
     * @param $secret
     * @return array|mixed
     */
    private function  decodeSecurityParams($paramsStr,$secret)
    {
        // 对加密的参数进行解密
        $aes = new CryptAES();
        $aes->set_key($secret);
        $aes->require_pkcs5();
        $decstr = $aes->decrypt($paramsStr);
        // 将解密后的json字符串转成数组
        $ret = empty($decstr) ? array() : json_decode($decstr, true);
        // 验证参数是否过期
        $timestamp = array_key_exists('_timestamp', $ret) ? $ret['_timestamp'] : 0;
        $timeout = array_key_exists('_timeout', $ret) ? $ret['_timeout'] : 0;
        if ($timestamp <= 0) {
            return array('errcode'=>FanweErrcode::OPEN_SIGNATURE_EXPIRE,'message'=>'签名超时');
        }
        if ($timeout > 0) {
            if (abs($timestamp - time()) > $timeout * 60) { // 误差超过指定时间，已超时
                return array('errcode'=>FanweErrcode::OPEN_SIGNATURE_EXPIRE,'message'=>'签名超时');
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
