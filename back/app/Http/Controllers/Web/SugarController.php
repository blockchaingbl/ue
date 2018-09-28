<?php

namespace App\Http\Controllers\Web;



use App\Helper;
use App\Http\Models\FreeLog;
use App\Http\Models\Sugar;
use App\Http\Models\SugarLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;





class SugarController extends BaseController
{
    //时间到未领取完的糖果退回
    public function back()
    {
        $lock = Cache::get("sugar_back");
        if($lock){
            exit('locked');
        }else{
            Cache::put("sugar_back","locked",5);
        }
        $sugars = Sugar::where(function ($query) {
            $query->where('less_amount', '>', '0');
            $query->where('receive_end_time', '<', date('Y-m-d H:i:s'));
        })->limit(20)->get();
        if(!$sugars->count()){
            Cache::forget("sugar_back");
            return 'finish';
        }
        DB::beginTransaction();
        try {
            $sugars->map(function ($val){
                Helper::incomeCoin('sugar_back',$val->id);
                $val->less_amount = 0;
                $val->copys_less = 0;
                $val->save();
            });
            DB::commit();
            Cache::forget("sugar_back");
        }catch (\Exception $e){
            DB::rollback();
            Cache::forget("sugar_back");
            Log::warn($e->getMessage());
            dump($e->getMessage());
            return "Error";
        }
    }
    //领取的糖果锁仓时间到了自动释放
    public function free()
    {
        $lock = Cache::get("sugar_free");
        if($lock){
            exit('locked');
        }else{
            Cache::put("sugar_free","locked",5);
        }
        $sugar_logs = SugarLog::where(function ($query){
            $query->where('lock_end_time','<',date('Y-m-d H:i:s'));
            $query->where('free',0);
        })->limit(20)->get();
        if(!$sugar_logs->count()){
            Cache::forget("sugar_free");
            return 'finish';
        }
        DB::beginTransaction();
        try {
            $sugar_logs->map(function ($val){
                /*释放日志*/
                $free_log = new FreeLog();
                $free_log->user_id = $val->to;
                $free_log->type = 'sugar';
                $free_log->relate = $val->id;
                $free_log->vc_amount = $val->sugar_amount;
                $free_log->vc_normal = $val->sugar_amount;
                $free_log->create_time = date('Y-m-d H:i:s');
                $free_log->coin_type = $val->coin_type;
                $free_log->detail = '释放'.Helper::formatCoin($val->sugar_amount,1).'糖果';
                $free_log->save();
                Helper::freeCoin($val->freeze_log_id);
                $val->free = 1;
                $val->save();
            });
            DB::commit();
            Cache::forget("sugar_free");
        }catch (\Exception $e){
            DB::rollback();
            Cache::forget("sugar_free");
            Log::warn($e->getMessage());
            return "Error";
        }
    }
}
