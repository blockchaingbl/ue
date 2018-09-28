<?php

namespace App\Open\Services;


use App\FanweErrcode;
use App\Helper;
use Illuminate\Support\Facades\Log;

class ServiceInvoker
{


    /**
     * 调用方维服务指定类的指定函数，返回数组
     *
     */
    public static function invoke($service,$param)
    {
        $api = self::getApi($service,$function);
        if(!$api)
        {
            return ["errcode"=>FanweErrcode::SYSTEM_ERROR,"message"=>"非法的API请求"];
        }
        if(!method_exists($api,$function))
        {
            return ["errcode"=>FanweErrcode::SYSTEM_ERROR,"message"=>"非法的API请求"];
        }

        $result = call_user_func_array([$api,"init"],[$param]);
        if($result['errcode']>0)return $result;

        $result = call_user_func_array([$api,$function],[$param]);
        if($api->log)
        {
            Helper::postLog($param,'api');
        }
        return $result;
    }


    private static function getApi($service,&$function)
    {
        $reg_text = "/^([A-Za-z]*)\.*([A-Za-z]*)[\/]*([A-Za-z]*)[\/]*([A-Za-z_]*)$/";
        if(!preg_match($reg_text,$service,$match))
        {
            return false;
        }

        $namespace = ucfirst(strtolower($match[1])).'\\'.ucfirst(strtolower($match[2]));

        if($match[3]!="")
            $class = ucfirst(strtolower($match[3]))."Api";
        else
            $class = "IndexApi";


        if($match[4]!="")
            $function = trim($match[4]);
        else
            $function = "index";


        $class_full = __NAMESPACE__ .'\\'.$namespace.'\\'.$class;
        if(class_exists($class_full))
            $api = new $class_full;
        return $api;
    }
}
