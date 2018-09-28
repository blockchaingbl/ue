<?php

namespace App\Http\Controllers\Web;


use App\Helper;
use App\Http\Models\FreezeLog;
use App\Http\Models\Otc;
use App\Http\Models\OtcOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class OrderController extends BaseController
{

    //订单取消
    public function cancelQueue()
    {
        try{
            set_time_limit(0);
            $lock = Cache::get("cancelqueue");
            if(!$lock)
            {
                Cache::put("cancelqueue","locked",5);
                DB::beginTransaction();
                try{
                    $overtime = db_config("OTC_ORDER_OVERTIME")*60; //订单超时时间
                    $otc_order = OtcOrder::where('status',0)->where('create_time','<=',time()-$overtime)->limit(10)->get(); //待付款订单
                    foreach($otc_order as $item)
                    {
                        //超过时间，取消订单
                        $order = OtcOrder::where("id",$item->id)->first();
                        $order->status = 3;
                        $order->cancel_time = time();
                        $order->cancel_memo = "付款超时平台取消";
                        $order->save();
                        Helper::userBreakCount($order->buyer);
                        //解锁金额
                        $otc = Otc::where(["id"=>$order->otc_id])->first();
                        $freeze_id = FreezeLog::where(["relate"=>$otc->id,"type"=>"sale"])->first()->id;
                        Helper::unlockCoin($freeze_id,$order->vc_amount + $order->vc_fee);
                        //返还otc挂卖交易单的剩余数量
                        $otc->vc_less_amount += $order->vc_amount;
                        $otc->vc_less_fee += $order->vc_fee;//返还剩余手续费
                        $otc->save();
                    }
                    DB::commit();
                    Cache::forget("cancelqueue");
                }catch(\Exception $e)
                {
                    DB::rollback();
                    Cache::forget("cancelqueue");
                    Log::warn($e->getMessage());
                    return "Error";
                }
            }
            else
            {
                return "locked";
            }
        }catch (\Exception $e)
        {
            Cache::forget("eventqueue");
            return "exception";
        }
    }
}
