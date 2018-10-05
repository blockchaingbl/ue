<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Http\Models\CoinType;
use App\Http\Models\FreezeLog;
use App\Http\Models\LockTransferLog;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;

use App\Http\Models\LockTransferAuth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LockTransferController extends AuthBaseController
{
    //权限
    public function auth(Request $request)
    {
        $param['coin_type'] = $request->input('coin_type',0);
        $param['keyword'] = $request->input('keyword');
        $param['date'] = $request->input('date');
        if($param['coin_type']==0){
            $lists = LockTransferAuth::leftJoin('user','user_id','=','user.id')
                ->select('lock_transfer_auth.*','user.vc_total','user.mobile')
                ->where(function ($query)use($param){
                    $query->where('lock_transfer_auth.coin_type','=',$param['coin_type']);
                    if($param['date']){
                        $dateStr = explode('~',$param['date']);
                        $begin_date = $dateStr[0];
                        $end_date = $dateStr[1];
                        $query->where('lock_transfer_auth.create_time','>=',$begin_date.' 00:00:00');
                        $query->where('lock_transfer_auth.create_time','<=',$end_date.' 23:59:59');
                    }
                    if($param['keyword']){
                        $query->where('user.mobile','like',"%{$param['keyword']}%");
                    }
                })->orderBy('id','desc')
                ->paginate(20);
        }else {
            $lists = LockTransferAuth::leftJoin('user', 'lock_transfer_auth.user_id', '=', 'user.id')->leftJoin('user_asset',function ($join){
                $join->on('lock_transfer_auth.user_id','=','user_asset.user_id');
                $join->on('lock_transfer_auth.coin_type','=','user_asset.coin_type');
            })
                ->select('lock_transfer_auth.*', 'user.mobile','user_asset.vc_total')
                ->where(function ($query)use($param){
                    $query->where('lock_transfer_auth.coin_type','=',$param['coin_type']);
                    if($param['date']){
                        $dateStr = explode('~',$param['date']);
                        $begin_date = $dateStr[0];
                        $end_date = $dateStr[1];
                        $query->where('lock_transfer_auth.create_time','>=',$begin_date.' 00:00:00');
                        $query->where('lock_transfer_auth.create_time','<=',$end_date.' 23:59:59');
                    }
                    if($param['keyword']){
                        $query->where('user.mobile','like',"%{$param['keyword']}%");
                    }
                })
                ->paginate(20);
        }
        $coin_type_all = CoinType::getAll();
        $selectCoin = $coin_type_all[$param['coin_type']];
        return view('manage.locktransfer.auth',['lists'=>$lists,'param'=>$param,'coin_type_all'=>$coin_type_all,'selectCoin'=>$selectCoin]);
    }

    public function save(Request $request)
    {
        $param = $request->all();
        if(!$param['user_id']){
            return $this->error('无此用户',FanweErrcode::USER_NOT_EXSITS);
        }
        if(!is_numeric($param['min_limit']))
        {
            return $this->error('请填写正确的资产限制');
        }
        if(!is_numeric($param['receive_fee']) || $param['receive_fee']<0){
            return $this->error('请填写正确的手续费比例');
        }
        if($param['auth_type']!=2 && !is_numeric($param['limit_day']))
        {
            return $this->error('请填写天数');
        }
        $recode = LockTransferAuth::where('coin_type',$param['coin_type'])->where('user_id',$param['user_id'])->first();
        if($recode){
            return $this->error('已为用户添加过资格，请确认');
        }
        if($param['auth_type']==2)
        {
            $param['limit_day']=0;
        }
        $sugerAuth = new LockTransferAuth();
        $sugerAuth->create_time = date('Y-m-d H:i:s');
        $sugerAuth->status = 1;
        $sugerAuth->min_limit = $param['min_limit'];
        $sugerAuth->receive_fee = $param['receive_fee'];
        $sugerAuth->coin_type = $param['coin_type'];
        $sugerAuth->user_id = $param['user_id'];
        $sugerAuth->auth_type = $param['auth_type'];
        $sugerAuth->limit_day = intval($param['limit_day']);
        $sugerAuth->cp_rate = $param['cp_rate'];
        $sugerAuth->invite_cp_rate =$param['invite_cp_rate'];
        $result = $sugerAuth->save();
        if($result){
            return $this->success();
        }else{
            return $this->error();
        }
    }

    public function log(Request $request)
    {
        $param = $request->all();
        $param['time_type'] = $request->input('time_type','1');
        $param['coin_type'] = $request->input('coin_type',0);
        $lists = LockTransferLog::with('from_user','to_user')
        ->where(function ($query)use($param){
            $query->where('coin_type',$param['coin_type']);
            if($param['from']){
                $from_user = User::where('mobile','=',$param['from'])->first();
                $query->where('from','=',$from_user->id);
            }
            $query->where('from','!=',0);
            if($param['to']){
                $from_user = User::where('mobile','=',$param['to'])->first();
                $query->where('to','=',$from_user->id);
            }
            if($param['date']){
                $dateStr = explode('~',$param['date']);
                $begin_date = $dateStr[0];
                $end_date = $dateStr[1];
                if($param['time_type']==1){
                    $query->where('create_time','>=',$begin_date.' 00:00:00');
                    $query->where('create_time','<=',$end_date.' 23:59:59');
                }elseif($param['time_type']==2){
                    $query->where('lock_end_time','>=',$begin_date.' 00:00:00');
                    $query->where('lock_end_time','<=',$end_date.' 23:59:59');
                }
            }
        })->paginate(20);
        $lists->map(function ($item){
            $time = strtotime($item->lock_end_time);
            $diff = $time-time();
            if($diff>0){
                $item->free_day = ceil(($diff)/86400);
            }else{
                if($item->free){
                    $item->free_day = '已释放';
                }else{
                    $item->free_day ='即将释放';
                }

            }
        });
        $coin_type_all = CoinType::getAll();
        return view('manage.locktransfer.log',['lists'=>$lists,'param'=>$param,'coin_type_all'=>$coin_type_all]);
    }

    function edit(Request $request){
        $param = $request->all();
        $sugar_auth = LockTransferAuth::find($param['id']);
        if(!$sugar_auth){
            return $this->error();
        }
        if($param['auth_type']!=2 && !is_numeric($param['limit_day']))
        {
            return $this->error('请填写天数');
        }
        if($param['auth_type']==2)
        {
            $param['limit_day']=0;
        }
        $sugar_auth->status = intval($param['status']);
        $sugar_auth->min_limit = $param['min_limit'];
        $sugar_auth->receive_fee = $param['receive_fee'];
        $sugar_auth->auth_type = $param['auth_type'];
        $sugar_auth->limit_day = intval($param['limit_day']);
        $sugar_auth->cp_rate = $param['cp_rate'];
        $sugar_auth->invite_cp_rate =$param['invite_cp_rate'];
        $result = $sugar_auth->save();
        if($result){
            return $this->success();
        }else{
            return $this->error();
        }
    }

    public function logs(Request $request)
    {
        $param = $request->all();
        $param['time_type'] = $request->input('time_type','1');
        $param['coin_type'] = $request->input('coin_type',0);
        $lists = LockTransferLog::with('from_user','to_user')
            ->where(function ($query)use($param){
                $query->where('coin_type',$param['coin_type']);
                $query->where('from','=',0);
                if($param['to']){
                    $from_user = User::where('mobile','=',$param['to'])->first();
                    $query->where('to','=',$from_user->id);
                }
                if($param['date']){
                    $dateStr = explode('~',$param['date']);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    if($param['time_type']==1){
                        $query->where('create_time','>=',$begin_date.' 00:00:00');
                        $query->where('create_time','<=',$end_date.' 23:59:59');
                    }elseif($param['time_type']==2){
                        $query->where('lock_end_time','>=',$begin_date.' 00:00:00');
                        $query->where('lock_end_time','<=',$end_date.' 23:59:59');
                    }
                }
            })->paginate(20);
        $lists->map(function ($item){
            $time = strtotime($item->lock_end_time);
            $diff = $time-time();
            if($diff>0){
                $item->free_day = ceil(($diff)/86400);
            }else{
                if($item->free){
                    $item->free_day = '已释放';
                }else{
                    $item->free_day ='即将释放';
                }

            }
        });
        $coin_type_all = CoinType::getAll();
        return view('manage.locktransfer.logs',['lists'=>$lists,'param'=>$param,'coin_type_all'=>$coin_type_all]);
    }

    public function cancel(Request $request)
    {
        $id = $request->input('id');
        $log = LockTransferLog::find($id);
        if(!$log)
        {
            return $this->error('不存在');
        }
        if($log->free==1)
        {
            return $this->error('已经释放');
        }

        DB::beginTransaction();
        try
        {
            $log->free = 1;
            $log->save();
            $user_asset = UserAsset::where(['user_id'=>$log->to,'coin_type'=>$log->coin_type])->first();
            $user_asset->vc_freeze -= $log->less_amount;
            $user_asset->save();
            $free_amount = $log->amount - $log->less_amount;
            if($log->coin_type==0)
            {
                $user = User::find($log->to);
                $user->vc_freeze -= $log->less_amount;
                $user->vc_freeze_normal -= $log->vc_normal;
                $user->vc_freeze_untrade -= $log->untrade;
                $user->save();
            }
            DB::commit();
            return $this->success("撤回成功 扣减{$log->less_amount}冻结金额,已释放{$free_amount}部分需要人工冻结");
        } catch (\Exception $e)
        {
            DB::rollback();
        return $this->error();
            return "Error";
        }


    }

    public function export(Request $request)
    {
        set_time_limit(0);
        $coin_type = intval($request->input('coin_type'));
        $lists = LockTransferLog::with('from_user','to_user')->where('coin_type',$coin_type)->get();
        $cellData = [['转账时间','转账人者','接收者','转账数量','转账手续费','剩余释放天数','已释放','每次释放','下次释放时间']];
        foreach($lists as $item)
        {

           if($item->lock_time!=0)
           {
               $evey_day = $item->amount/$item->lock_time;
           }else{
               $evey_day = $item->amount;
           }
            $cellData[] = [
                $item->create_time,$item->from_user->mobile,$item->to_user->mobile,$item->amount,
                $item->lock_transfer_fee,$item->free_day,$item->amount-$item->less_amount,$evey_day
                ,date('Y-m-d H:i:s',strtotime($item->last_release_time)+86400)
            ];
        }
        try{
            Excel::create('锁仓转账',function($excel) use ($cellData){
                $excel->sheet('score', function($sheet) use ($cellData){
                    $sheet->rows($cellData);
                });
            })->export('xls');
            return $this->success();
        }catch(\Exception $e){
            Log::warn($e->getMessage());
        }
    }

    public function export_incharge()
    {
        set_time_limit(0);
        $lists = LockTransferLog::with('to_user')
        ->where('coin_type',0)
        ->where('from',0)->get();
        $cellData = [['充值时间','接收者','数量	','剩余未释放','剩余释放天数']];
        foreach($lists as $item)
        {
            $cellData[] = [
                $item->create_time,$item->to_user->mobile,$item->amount,
                $item->amount-$item->less_amount,
                $item->amount/$item->lock_time
            ];
        }
        try{
            Excel::create('锁仓兑换',function($excel) use ($cellData){
                $excel->sheet('score', function($sheet) use ($cellData){
                    $sheet->rows($cellData);
                });
            })->export('xls');
            return $this->success();
        }catch(\Exception $e){
            Log::warn($e->getMessage());
        }
    }
}
