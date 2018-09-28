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
use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Models\Pony;
use App\Http\Models\PonyPartColor;
use App\Http\Models\PonyPartConfig;
use App\Http\Models\Web\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

include(base_path("app/Library/Ethereum/ethereum.php"));
class BaseController extends Controller
{

    public $user;
    public function __construct()
    {
        $pid = intval($_REQUEST['pid']);
        $this->fetchReferuser($pid);

        if (Auth::check()) {
            $this->user = Auth::user();
            View::share("user",$this->user);
        }


        $current_route = $GLOBALS['app']->getCurrentRoute();
        View::share("current_route",$current_route["as"]);
    }

    //获取推荐人
    protected function fetchReferuser($pid){
        if($pid>0)
        {
            $ref_user = User::where(["id"=>$pid])->first();
            if($ref_user)
                Session::put("pid",$pid);
            else
            {
                $pid = 0; //没有用户不产生推荐人
            }
        }
        else
        {
            $pid = Session::get("pid");
        }
        View::share("pid",$pid);
        return $pid;
    }

    protected function success($msg,$data,$ajax)
    {
        if($ajax==1)
        {
            if($msg == "")$msg = "操作成功";
            return ["errcode"=>FanweErrcode::SYSTEM_SUCCESS,"message"=>$msg,"data"=>$data];
        }
        else
        {
            return $msg;
        }
    }

    protected function error($msg,$data,$ajax,$code = FanweErrcode::SYSTEM_ERROR)
    {
        if($ajax==1)
        {
            if($msg == "")$msg = "操作失败";
            return ["errcode"=>$code,"message"=>$msg,"data"=>$data];
        }
        else
        {
            return $msg;
        }
    }
}