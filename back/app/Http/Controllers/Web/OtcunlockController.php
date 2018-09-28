<?php

namespace App\Http\Controllers\Web;



use App\Helper;
use App\Http\Models\FreeLog;
use App\Http\Models\FreezeLog;
use App\Http\Models\OtcOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;




class OtcunlockController extends BaseController
{
    
    public function otcfree(Request $request)
    {
        $sort = $request->input('sort','asc');
        $offset = $request->input('offset',0);
        $real_offset = 30*$offset;
        $date = date('Y-m-d H:i:s',strtotime('-1 day'));
        $lock = Cache::get('otcunlock'.$offset);
        if(!$lock)
        {
            Cache::put('otcunlock'.$offset,'lock');
        }
        else
        {
            return 'lock';
        }
        $otc_orders = OtcOrder::where(function ($query)use($date){
            $query->where('is_lock','=',1);
            $query->where('free','=',0);
            $query->where('less_amount','>',0);
            $query->where('last_release_time','<',$date);
            $query->where('status','=',2);
        })->orderBy('id',$sort)->offset($real_offset)->limit(20)->get();
        if(!$otc_orders->count())
        {
            Cache::forget('otcunlock'.$offset);
            return 'finish';
        }

        DB::beginTransaction();
        try
        {
            $otc_orders->map(function ($val){
                /*计算应该释放次数*/
                $should_time =intval((time()-$val->create_time)/86400);//此时应该释放了多少次
                $diff = $should_time-($val->lock_day - $val->remain_time);
                if($diff<=0){
                    return false;
                }
                if($diff>$val->remain_time){
                     $val->remain_time = $diff;
                }
                $time_free = $val->vc_amount / $val->lock_day;
                $free_total = $time_free*$diff;
                if($free_total>$val->less_amount){
                    $free_total = $val->less_amount;
                }
                if($val->remain_time==1)
                {
                    $free_total = $val->less_amount;
                }
                $val->remain_time -= $diff;
                $val->less_amount -=$free_total;
                $date_time = explode(' ',date('Y-m-d H:i:s',$val->create_time))[1];
                $val->last_release_time = date('Y-m-d')." {$date_time}";
                if($val->less_amount==0)
                {
                    $val->free = 1;
                }
                $val->save();
                /*释放日志*/
                $free_log = new FreeLog();
                $free_log->user_id = $val->buyer;
                $free_log->type = 'otc_lock';
                $free_log->relate = $val->id;
                $free_log->vc_amount = $free_total;
                $free_log->vc_normal = $free_total;
                $free_log->create_time = date('Y-m-d H:i:s');
                $free_log->coin_type = 0;
                $free_log->detail = '释放'.Helper::formatCoin($free_total,1).'锁仓otc订单';
                $free_log->save();
                Helper::free_lock($free_log->id);
                $freeze_log = FreezeLog::where('id',$val->freeze_log_id)->first();
                if(!$freeze_log){
                    throw new \Exception("freeze_log not exist:".$val->freeze_log_id);
                }
                $freeze_log->vc_done_amount+=$free_total;
                $freeze_log->vc_done_normal+=$free_total;
                if($freeze_log->vc_done_amount == $freeze_log->vc_done_normal){
                    $freeze_log->free_time = date('Y-m-d H:i:s');
                    $freeze_log->status = 1;
                }
                if($freeze_log->vc_done_amount>$freeze_log->vc_amount)
                {
                    throw new \Exception("vc_done_amount>vc_amount:".$freeze_log->id);
                }
                $freeze_log->save();
            });
            DB::commit();
            Cache::forget('transunlock'.$offset);
            return 'ok';
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Cache::forget('transunlock'.$offset);
            Log::warn($e->getMessage());
            return "Error";
        }
    }

}
