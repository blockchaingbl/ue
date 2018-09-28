<?php

namespace App\Http\Controllers\Web;




use App\Http\Models\CalcLog;
use App\Http\Models\ExpendLog;
use App\Http\Models\IncomeLog;
use App\Http\Models\LockTransferLog;
use App\Http\Models\Web\User;

class CalcController extends BaseController
{
    public function calc()
    {
        set_time_limit(0);
        $lock = Cache::get("calc");
        if(!$lock)
        {
            Cache::put("calc","locked",5);
            DB::beginTransaction();
        }else{
            return 'lock';
        }
        $time = date('Y-m-d');
        $user = User::where('level','>=',1)->where('calc_time','<',$time)->orderBy('calc_time','asc')->first();

        if($user)
        {
            CalcLog::calc_user_relation($user);
            $user->calc_time = date('Y-m-d');
            $user->save();
        }
    }

    public function calc_day_amount()
    {
        set_time_limit(0);
        $calc_log = CalcLog::where('status',0)->orderBy('create_time','desc')->first();
        if($calc_log)
        {
            $ids = json_decode($calc_log->children);
            $less_amount = LockTransferLog::whereIn('to',$ids)->where('from',0)->where('coin_type',0)->sum('less_amount');
            $user = User::find($calc_log->user_id);
            $user->incharge_less_amount = $less_amount;
            $user->incharge_less_amount_time = date('Y-m-d H:i:s');

            $time_start = $calc_log->calc_date.' 00:00:00';
            $time_end = $calc_log->calc_date.' 23:59:59';
            $type = ['lock_transfer','lock_transfer_fee','trans_order'];
            $expend_amount = ExpendLog::whereIn('user_id',$ids)->whereIn('type',$type)
            ->where('create_time','>=',$time_start)
            ->where('create_time','<=',$time_end)
            ->sum('vc_amount');
            $buy_amount = whereIn('user_id',$ids)->whereIn('type','purchase')
                ->where('create_time','>=',$time_start)
                ->where('create_time','<=',$time_end)
                ->sum('vc_amount');
            $expend_amount +=$buy_amount;
            $incharge_today_amount = LockTransferLog::whereIn('to',$ids)->where('from',0)->where('coin_type',0)
            ->where('create_time','>=',$time_start)
            ->where('create_time','<=',$time_end)
            ->sum('amount');

            $inchage_amount= LockTransferLog::whereIn('to',$ids)->where('from',0)->where('coin_type',0)->sum('amount');
            $calc_log->incharge_less_amount = $less_amount;
            $calc_log->expend_amount = $expend_amount;
            $calc_log->incharge_today_amount = $incharge_today_amount;
            $calc_log->inchage_amount = $inchage_amount;
            $calc_log->status = 1;
            $calc_log->save();
//            if($calc_log->forcheck)
//            {
//                $kid_amount = $calc_log->incharge_amount;
//                $pid = $user->attached? $user->attached: $user->pid;
//                if($pid)
//                {
//                    $p_calc_log = CalcLog::where(['user_id'=>$pid,'status'=>1])->orderBy('id','desc');
//                    if($kid_amount/$p_calc_log->incharge_amount > 0.5)
//                    {
//                        $user->level = 3;
//                    }
//                }
//            }
            $user->save();

        }
    }

    public function calc_month_amount()
    {
        set_time_limit(0);

        $calc_log = CalcLog::where('month_status',1)->orderBy('create_time','asc')->first();
        if(!$calc_log)
        {
            return 'finish';
        }
        $ids = json_decode($calc_log->children_month);
        $time_start = date('Y-m-01', strtotime(date("Y-m-d"))).' 00:00:00';
        $time_end =date('Y-m-d', strtotime("$time_start +1 month -1 day"));
        $amount = LockTransferLog::whereIn('to',$ids)
        ->where('from',0)->where('coin_type',0)
        ->where('create_time','>=',$time_start)
        ->where('create_time','<=',$time_end)
        ->sum('amount');


       
        $expend_amount = ExpendLog::whereIn('user_id',$ids)->where('type','transfer_order')
        ->where('create_time','>=',$time_start)
        ->where('create_time','<=',$time_end)->sum('vc_amount');
        $calc_log->month_expend_amount = $expend_amount;
        $calc_log->month_incharge_amount = $amount;
        $calc_log->month_update_time = date('Y-m-d H:i:s');
        $calc_log->month_status = 2;
        $calc_log->save();
    }

    public function checkUser()
    {
        set_time_limit(0);
        $user = User::where('incharge_less_amount','>',200000)
        ->where('incharge_less_amount','<',300000)
        ->where('check_time',date('Y-m-d',strtotime('-15days')))
        ->orderBy('check_time')->first();
        if($user)
        {
            $lists =User::whereRaw("(attached = ?) OR (attached = 0 AND pid = ?) AND level <3",[$user->id,$user->id])->get();
            $ids = [];

            $lists->map(function ($val)use (&$ids){
               CalcLog::calc_user_relation($val,1);
            });
        }else{
            return 'finish';
        }
    }


}
