<?php

namespace App\Open\Controllers\Open;

use App\FanweErrcode;
use App\Helper;
use App\Open\Services\ServiceInvoker;
use App\Open\Controllers\Controller as BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache,DB,Config;


class DocController extends BaseController
{

    //
    public function index(Request $request,$group,$type,$class,$func)
    {
        $api_service = $group.".".$type;
        $filename = ucfirst(strtolower($group))."/".ucfirst(strtolower($type));
        if($class)
        {
            $api_service.="/".$class;
            $filename.="/".ucfirst(strtolower($class))."Api.php";
        }
        else
        {
            $filename.="/IndexApi.php";
        }

        $filename = base_path("app/Open/Services/").$filename;

        if($func&&$func!="index")
            $api_service.="/".$func;
        $info['接口service'] = $api_service;
        $info['ajax方式'] = "http://www.".config("app.route_domain")."/api/".$api_service;
        $info['remote方式'] = "http://open.".config("app.route_domain")."/".$api_service;

        $content = file_get_contents($filename);
//        echo $content;exit;
        preg_match("/\/\*\*[^@]+@name[\s]+".$func."[^@]+@description[\s]+([^@]+)@param([^@]+)@return([^\/]+)/",$content,$matches);
        if($matches)
        {
            $info['说明'] = str_replace("*","",$matches[1]);
            $info['传入参数'] = str_replace("*","",$matches[2]);
            $info['输出参数'] = str_replace("*","",$matches[3]);
        }

        return view("doc.index",["info"=>$info]);
    }

    public function all()
    {
        $files = [];
        $root = base_path("app/Open/Services/");
        $services_dirs = scandir($root);
        foreach($services_dirs as $group_dir)
        {
            if($group_dir!="."&&$group_dir!=".."&&is_dir($root.$group_dir))
            {
                $type_dirs = scandir($root.$group_dir);
                foreach($type_dirs as $type_dir)
                {

                    if($type_dir!="."&&$type_dir!=".."&&is_dir($root.$group_dir."/".$type_dir))
                    {
                        $file_items = scandir($root.$group_dir."/".$type_dir);
                        foreach($file_items as $file)
                        {
                            if($file!="."&&$file!="..")
                            {
                                $file_path = $root.$group_dir."/".$type_dir."/".$file;

                                $file_content = file_get_contents($file_path);
                                preg_match_all("/function[\s]+([^\(]+)\(/",$file_content,$matches);
                                if($matches)
                                {
                                    foreach($matches[1] as $function)
                                    {
                                        $filekey = strtolower($group_dir).".".strtolower($type_dir)."/".strtolower(str_replace("Api.php","",$file));
                                        if($function!="index")
                                        $filekey.="/".trim($function);
                                        $files[] = "<a href='".url($filekey)."'>".$filekey."</a>";
                                    }

                                }

                            }
                        }

                    }

                }
            }
        }
        return view("doc.all",["apis"=>$files]);
    }


}
