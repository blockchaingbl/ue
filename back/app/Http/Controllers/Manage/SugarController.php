<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Http\Models\CoinType;
use App\Http\Models\SugarLog;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;

use App\Http\Models\SugarAuth;

class SugarController extends AuthBaseController
{
    //发糖权限
    public function auth(Request $request)
    {
        $param['coin_type'] = $request->input('coin_type',0);
        $param['keyword'] = $request->input('keyword');
        $param['date'] = $request->input('date');
        if($param['coin_type']==0){
            $lists = SugarAuth::leftJoin('user','user_id','=','user.id')
                ->select('sugar_auth.*','user.vc_total','user.mobile')
                ->where(function ($query)use($param){
                    $query->where('sugar_auth.coin_type','=',$param['coin_type']);
                    if($param['date']){
                        $dateStr = explode('~',$param['date']);
                        $begin_date = $dateStr[0];
                        $end_date = $dateStr[1];
                        $query->where('sugar_auth.create_time','>=',$begin_date.' 00:00:00');
                        $query->where('sugar_auth.create_time','<=',$end_date.' 23:59:59');
                    }
                    if($param['keyword']){
                        $query->where('user.mobile','like',"%{$param['keyword']}%");
                    }
                })
                ->paginate(20);
        }else {
            $lists = SugarAuth::leftJoin('user', 'sugar_auth.user_id', '=', 'user.id')->leftJoin('user_asset',function ($join){
                $join->on('sugar_auth.user_id','=','user_asset.user_id');
                $join->on('sugar_auth.coin_type','=','user_asset.coin_type');
            })
                ->select('sugar_auth.*', 'user.mobile','user_asset.vc_total')
                ->where(function ($query)use($param){
                    $query->where('sugar_auth.coin_type','=',$param['coin_type']);
                    if($param['date']){
                        $dateStr = explode('~',$param['date']);
                        $begin_date = $dateStr[0];
                        $end_date = $dateStr[1];
                        $query->where('sugar_auth.create_time','>=',$begin_date.' 00:00:00');
                        $query->where('sugar_auth.create_time','<=',$end_date.' 23:59:59');
                    }
                    if($param['keyword']){
                        $query->where('user.mobile','like',"%{$param['keyword']}%");
                    }
                })
                ->paginate(20);
        }
        $coin_type_all = CoinType::getAll();
        $selectCoin = $coin_type_all[$param['coin_type']];
        return view('manage.sugar.auth',['lists'=>$lists,'param'=>$param,'coin_type_all'=>$coin_type_all,'selectCoin'=>$selectCoin]);
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
        $recode = SugarAuth::where('coin_type',$param['coin_type'])->where('user_id',$param['user_id'])->first();
        if($recode){
            return $this->error('已为用户添加过发糖资格，请确认');
        }
        $sugerAuth = new SugarAuth();
        $sugerAuth->create_time = date('Y-m-d H:i:s');
        $sugerAuth->status = 1;
        $sugerAuth->min_limit = $param['min_limit'];
        $sugerAuth->receive_fee = $param['receive_fee'];
        $sugerAuth->coin_type = $param['coin_type'];
        $sugerAuth->user_id = $param['user_id'];
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
        $lists = SugarLog::with('from_user','to_user')
        ->where(function ($query)use($param){
            $query->where('coin_type',$param['coin_type']);
            if($param['from']){
                $from_user = User::where('mobile','=',$param['from'])->first();
                $query->where('from','=',$from_user->id);
            }
            if($param['to']){
                $from_user = User::where('mobile','=',$param['to'])->first();
                $query->where('to','=',$from_user->id);
            }
            if($param['sugar_num']){
                $query->where('sugar_num',$param['sugar_num']);
            }
            if($param['date']){
                $dateStr = explode('~',$param['date']);
                $begin_date = $dateStr[0];
                $end_date = $dateStr[1];
                if($param['time_type']==1){
                    $query->where('create_time','>=',$begin_date.' 00:00:00');
                    $query->where('create_time','<=',$end_date.' 23:59:59');
                }elseif($param['time_type']==2){
                    $query->where('sugar_time','>=',$begin_date.' 00:00:00');
                    $query->where('sugar_time','<=',$end_date.' 23:59:59');
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
        return view('manage.sugar.log',['lists'=>$lists,'param'=>$param,'coin_type_all'=>$coin_type_all]);
    }

    function edit(Request $request){
        $param = $request->all();
        $sugar_auth = SugarAuth::find($param['id']);
        if(!$sugar_auth){
            return $this->error();
        }
        $sugar_auth->status = intval($param['status']);
        $sugar_auth->min_limit = $param['min_limit'];
        $sugar_auth->receive_fee = $param['receive_fee'];
        $result = $sugar_auth->save();
        if($result){
            return $this->success();
        }else{
            return $this->error();
        }
    }
}
