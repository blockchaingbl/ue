<?php

namespace App\Http\Controllers\Web;


use App\FanweErrcode;
use App\Helper;
use App\Http\Models\EthereumAddress;
use App\Http\Models\SubscribeToken;
use Illuminate\Http\Request;


class ApiController extends BaseController
{
    public function index(Request $request,$group,$type,$class,$func)
    {
        $service = $group.".".$type."/".$class."/".$func;
        return Helper::invoke($service,$request->all());
    }


    //队列
    public function downblock(Request $request){
        $result = Helper::invoke("cron.transaction/syncblock/downblock",["block_chain"=>$request->input("block_chain")]);
        return $result;
    }

    public function repairblock(Request $request){
        $result = Helper::invoke("cron.transaction/syncblock/repairblock",["block_chain"=>$request->input("block_chain")]);
        return $result;
    }

    public function confirm(Request $request)
    {
        return Helper::invoke("cron.transaction/sync/confirm");
    }

    public function confirmtoken(Request $request)
    {
        return Helper::invoke("cron.transaction/synctoken/confirm");
    }

    public function push(Request $request)
    {
        return Helper::invoke("cron.transaction/sync/push");
    }

    public function pushtoken(Request $request)
    {
        return  Helper::invoke("cron.transaction/synctoken/push");
    }
    public function withdraw(Request $request)
    {
        return  Helper::invoke("cron.transaction/syncblock/withdraw");
    }
}
