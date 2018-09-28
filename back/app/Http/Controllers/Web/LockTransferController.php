<?php

namespace App\Http\Controllers\Web;



use App\Helper;
use App\Http\Models\FreeLog;
use App\Http\Models\FreezeLog;
use App\Http\Models\LockTransferLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;




class LockTransferController extends BaseController
{
    
    public function transunlock(Request $request)
    {
        $sort = $request->input('sort','asc');
        $offset = $request->input('offset',0);
        $real_offset = 30*$offset;
        $date = date('Y-m-d H:i:s',strtotime('-1 day'));
        $date_week = date('Y-m-d H:i:s',strtotime('-1 week'));
        $date_month = date('Y-m-d H:i:s',strtotime('-1 month'));
        $lock = Cache::get('transunlock'.$offset);
        if(!$lock)
        {
            Cache::put('transunlock'.$offset,'lock');
        }
        else
        {
            return 'lock';
        }
        $lock_transfer_logs = LockTransferLog::where(function ($query)use($date,$date_week,$date_month){
            $query->where('free','=',0);
            $query->where('less_amount','>',0);
            $query->whereRaw("((last_release_time < '{$date}' and sugar_type = 'day') OR (last_release_time <'{$date_week}' and sugar_type = 'week') OR (last_release_time <'{$date_month}' and sugar_type = 'month'))");
        })->orderBy('id',$sort)->offset($real_offset)->limit(20)->get();

        if(!$lock_transfer_logs->count())
        {
            Cache::forget('transunlock'.$offset);
            return 'finish';
        }

        DB::beginTransaction();
        try
        {
            $lock_transfer_logs->map(function ($val){
                /*计算应该释放次数*/
                if($val->sugar_type=='week'){
                    $time_mul = 7;
                }else{
                    $time_mul = 1;
                }
                $should_time =intval((time()-strtotime($val->create_time))/(86400*$time_mul));//此时应该释放了多少次
                if($val->sugar_type=='month'){
                    $should_time = intval($this->getMonthNum(date('Y-m-d H:i:s'),$val->create_time));
                }
                $diff = $should_time-($val->lock_time - $val->remain_time);
                if($diff<=0){
                    return false;
                }
                if($diff>$val->remain_time){
                     $val->remain_time = $diff;
                }
                $time_free = $val->amount / $val->lock_time;
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
                $date_time = explode(' ',$val->create_time)[1];
                $val->last_release_time = date('Y-m-d')." {$date_time}";
                if($val->less_amount==0)
                {
                    $val->free = 1;
                }
                $val->save();
                /*释放日志*/
                $free_log = new FreeLog();
                $free_log->user_id = $val->to;
                $free_log->type = 'lock_transfer';
                $free_log->relate = $val->id;
                $free_log->vc_amount = $free_total;
                $free_log->vc_normal = $free_total;
                $free_log->create_time = date('Y-m-d H:i:s');
                $free_log->coin_type = $val->coin_type;
                $free_log->detail = '释放'.Helper::formatCoin($free_total,1).'锁仓资产';
                $free_log->save();
                Helper::free_lock($free_log->id);
                $freeze_log = FreezeLog::where('id',$val->freeze_log_id)->first();
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
    function getMonthNum($date1,$date2){
        $date1_stamp=strtotime($date1);
        $date2_stamp=strtotime($date2);
        list($date_1['y'],$date_1['m'])=explode("-",date('Y-m',$date1_stamp));
        list($date_2['y'],$date_2['m'])=explode("-",date('Y-m',$date2_stamp));
        return ($date_1['y']-$date_2['y'])*12 +$date_1['m']-$date_2['m'];
    }
}
