<?php

namespace App\Http\Controllers\Manage;


use App\Http\Models\Apply;
use App\Http\Models\ApplyNode;
use App\Http\Models\ApplyTeacher;
use App\Http\Models\CourseOrder;
use App\Http\Models\LockTransferAuth;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;
use App\Http\Models\CourseAuth;
use Illuminate\Support\Facades\DB;


class ApplyController extends AuthBaseController
{



    public function index(Request $request)
    {
        $param = $request->all();
        $param['status']  = $request->input('status','all');
        $lists = Apply::with('user')->where(function ($query)use ($param){
            if($param['from'])
            {
                $ids =  User::where('mobile','like',"%{$param['from']}%")
                    ->orWhere('username','like',"%{$param['from']}%")->lists('id');
                $query->whereIn('user_id',$ids);
                $query->orWhere('name','like',"%{$param['from']}%");
                $query->orWhere('mobile','like',"%{$param['from']}%");
            }
            if($param['status']!=='all')
            {
                $query->where('status',$param['status']);
            }
            if($param['date']){
                $dateStr = explode('~',$param['date']);
                $begin_date = $dateStr[0];
                $end_date = $dateStr[1];
                $query->where('update_time','>=',$begin_date.' 00:00:00');
                $query->where('update_time','<=',$end_date.' 23:59:59');
            }
        })->orderBy('update_time','desc')->paginate(20);
        $lists->map(function ($val){
            if($val->user->pid)
            {
                $val->parent = User::find($val->id);
            }
            return $val;
        });

        return view('manage.apply.index',['lists'=>$lists,'param'=>$param]);
    }

    public function apply(Request $request)
    {
        $param = $request->all();
        if(!$param['user_id'])
        {
            return $this->error('参数错误');
        }
        $apply = Apply::where('user_id',$param['user_id'])->first();
        if(!$apply)
        {
            return $this->error('参数错误');
        }
        if(!$param['memo'])
        {
            return $this->error('请填写理由');
        }

        DB::beginTransaction();
        try {
            $apply->status = $param['status'];
            $apply->memo = $param['memo'];
            if($param['status']==1)
            {
               User::where('id',$param['user_id'])->update(['level'=>1]);
            }
            if($param['status']==1 && $param['lock_transfer']==1)
            {
                $lock_transfer = LockTransferAuth::where(['user_id'=>$param['user_id']])->first();
                if(!$lock_transfer)
                {
                    $lock_transfer = new LockTransferAuth();
                }
                if($lock_transfer->status!=1)
                {
                    $lock_transfer->user_id = $param['user_id'];
                    $lock_transfer->status = 1;
                    $lock_transfer->coin_type = 0;
                    $lock_transfer->receive_fee = 0;
                    $lock_transfer->min_limit = 0;
                    $lock_transfer->create_time = date('Y-m-d H:i:s');
                    $lock_transfer->limit_day = 150;
                    $lock_transfer->auth_type = 1;
                    $lock_transfer->save();
                }
            }

            $apply->save();
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::warn($e->getMessage());
            return $this->error('系统错误,请稍后再试');
        }


    }


    public function node(Request $request)
    {
        $param = $request->all();
        $param['status']  = $request->input('status','all');
        $lists = ApplyNode::with('user')->where(function ($query)use ($param){
            if($param['from'])
            {
                $ids =  User::where('mobile','like',"%{$param['from']}%")
                    ->orWhere('username','like',"%{$param['from']}%")->lists('id');
                $query->whereIn('user_id',$ids);
                $query->orWhere('name','like',"%{$param['from']}%");
                $query->orWhere('mobile','like',"%{$param['from']}%");
            }
            if($param['status']!=='all')
            {
                $query->where('status',$param['status']);
            }
            if($param['date']){
                $dateStr = explode('~',$param['date']);
                $begin_date = $dateStr[0];
                $end_date = $dateStr[1];
                $query->where('update_time','>=',$begin_date.' 00:00:00');
                $query->where('update_time','<=',$end_date.' 23:59:59');
            }
        })->orderBy('update_time','desc')->paginate(20);
        $lists->map(function ($val){
            if($val->user->pid)
            {
                $val->parent = User::find($val->id);
            }
            return $val;
        });
        return view('manage.apply.node',['lists'=>$lists,'param'=>$param]);
    }

    public function node_apply(Request $request)
    {
        $param = $request->all();
        if(!$param['user_id'])
        {
            return $this->error('参数错误');
        }
        $apply = ApplyNode::where('user_id',$param['user_id'])->first();
        if(!$apply)
        {
            return $this->error('参数错误');
        }
        if(!$param['memo'])
        {
            return $this->error('请填写理由');
        }

        if($param['status']==1)
        {
            $user = User::where('id',$param['user_id'])->first();
            if($user->level==0)
            {
                $user->level =2;
                $user->save();
            }
            if($param['lock_transfer']==1)
            {
                $lock_transfer = LockTransferAuth::where(['user_id'=>$param['user_id']])->first();
                if(!$lock_transfer)
                {
                    $lock_transfer = new LockTransferAuth();
                }
                if($lock_transfer->status!=1)
                {
                    $lock_transfer->user_id = $param['user_id'];
                    $lock_transfer->status = 1;
                    $lock_transfer->coin_type = 0;
                    $lock_transfer->receive_fee = 0;
                    $lock_transfer->min_limit = 0;
                    $lock_transfer->create_time = date('Y-m-d H:i:s');
                    $lock_transfer->limit_day = 150;
                    $lock_transfer->auth_type = 1;
                    $lock_transfer->save();
                }
            }
        }
        DB::beginTransaction();
        try {
            $apply->status = $param['status'];
            $apply->memo = $param['memo'];
            $apply->save();
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::warn($e->getMessage());
            return $this->error('系统错误,请稍后再试');
        }


    }



}
