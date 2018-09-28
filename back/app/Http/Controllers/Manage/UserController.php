<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\ExpendLog;
use App\Http\Models\FlowApply;
use App\Http\Models\FreezeLog;
use App\Http\Models\IncomeLog;
use App\Http\Models\Manage\Admin;
use App\Http\Models\PromoterReward;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends AuthBaseController
{

    //用户管理
    public function index(Request $request)
    {
        return view("manage.user.index");
    }

    //用户管理
    public function getUserList(Request $request)
    {
        $param = $request->all();
        $sort_key_whitelist = [
            "id","create_time","vc_total","vc_normal","vc_untrade","vc_freeze",
            "vc_freeze_normal","vc_freeze_untrade","cp_total","cp_trade","cp_bonus",
            'achievement'
        ];
        $sort_key = $param['sort_key'];
        $sort_type = strtolower($param['sort_type'])=="desc"?"desc":"asc";
        if(!in_array($sort_key,$sort_key_whitelist))
        {
            $sort_key = "id";
        }
        $list = User::leftJoin("user as ref","ref.id","=","user.pid")
            ->select("user.*","ref.username as ref_name")
            ->where(function($query) use($param){
                if($param["username"])
                {
                    $query->whereRaw("fanwe_user.username like'%".$param["username"]."%' or fanwe_user.mobile like '".$param["username"]."%'");
                }
                if($param['ref_name'])
                {
                    $pids = [];
                    $pid_data = User::where("username","like","%".$param['ref_name']."%")->orWhere("mobile","like","%".$param['ref_name']."%")->get();
                    foreach($pid_data as $pid)
                    {
                        $pids[] = $pid->id;
                    }
                    $query->whereIn("user.pid",$pids)->whereRaw("fanwe_user.pid is not null");
                }
                if($param['create_time'])
                {
                    $dateStr = explode('~',$param['create_time']);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    if($begin_date)
                    {
                        $query->where("user.create_time",">",$begin_date." 00:00:00");
                    }
                    if($end_date)
                    {
                        $query->where("user.create_time","<",$end_date." 23:59:59");
                    }
                }
                if($param['status'])
                {
                    if($param['status']=='normal')
                    {
                        $query->where("user.status",1);
                    }
                    else
                    {
                        $query->where("user.status",0);
                    }
                }
                if($param['flow_status']){
                    if($param['flow_status']==2){
                        $query->where('user.flow_status',$param['flow_status']);
                    }else{
                        $query->where('user.flow_status','!=',$param['flow_status']);
                    }
                }
                if($param['level']!==''){
                    $query->where('user.level',$param['level']);
                }
            });
        if($param["coin_type"]>0)
        {
            $list = $list->leftJoin("user_asset",function($join) use($param){
                $join->on("user_asset.user_id","=","user.id")->where("user_asset.coin_type","=",$param["coin_type"]);
            })->addSelect(
                DB::raw("ifnull(fanwe_user_asset.vc_total,0) as vc_total"),
                DB::raw("ifnull(fanwe_user_asset.vc_freeze,0) as vc_freeze")
            );
        }
        $total = $list->count();
        $list = $list->orderBy($sort_key,$sort_type)->paginate(10);
        $userlist = $list->toArray();
        $userlist = $userlist['data'];
        $coin_list = Helper::getCoinType();
        $otc_open = config("app.otc");
        $page = $request->input('page',1);
        return ["list"=>$userlist,"total"=>$total,"pagehtml"=>$list->render(),"coin_list"=>$coin_list,"coin_type"=>$param["coin_type"],"otc_open"=>$otc_open,'page'=>$page];
    }

    //冻结,解冻用户
    public function lock(Request $request){
        $id = $request->input('id');
        $user = User::find($id);
        if(!$user){
            return parent::error('无此用户',FanweErrcode::USER_NOT_EXSITS);
        }

        return User::updateInfo(['id'=>$id,'status'=>$request->input('status')]);
    }

    //冻结/充值资金
    public function money(Request $request){
        $id = $request->input('id');
        $user = User::find($id);
        if(!$user){
            return response()->json(['msg'=>'无此用户','code'=>FanweErrcode::USER_NOT_EXSITS]);
        }
        $normal_money = $request->input('normal_money');
        $untrade_money = $request->input('untrade_money');
        $change_money = $request->input('change_money');
        $coin_type = $request->input('coin_type');
        $type = $request->input('type');
        switch ($type){
            case 1://冻结资金
                if(config("app.otc"))
                {
                    //otc开启，并且是平台币
                    if($coin_type==0)
                    {
                        if($normal_money>$user->vc_normal){
                            return $this->error('要冻结的可交易金额超出',FanweErrcode::SYSTEM_ERROR);
                        }
                        if($untrade_money>$user->vc_untrade){
                            return $this->error('要冻结的不可交易金额超出',FanweErrcode::SYSTEM_ERROR);
                        }
                        try{
                            DB::beginTransaction();
                            Helper::freezeCoin('manual',0,$normal_money,$untrade_money,$user->id,'后台冻结',$coin_type);
                            DB::commit();
                        }   catch (\Exception $e)
                        {
                            DB::rollback();
                            return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                        }
                    }
                    else
                    {
                        //非平台币
                        $user_asset = UserAsset::where(["user_id"=>$user->id,"coin_type"=>$coin_type])->first();
                        if($change_money>$user_asset->vc_total){
                            return $this->error('要冻结的金额超出账户余额',FanweErrcode::SYSTEM_ERROR);
                        }
                        try{
                            DB::beginTransaction();
                            Helper::freezeCoin('manual',0,0,$change_money,$user->id,'后台冻结',$coin_type);
                            DB::commit();
                        }   catch (\Exception $e)
                        {
                            DB::rollback();
                            return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                        }
                    }
                }
                else
                {
                    //otc关闭
                    if($coin_type==0)
                    {
                        //平台币
                        if($change_money>$user->vc_total){
                            return $this->error('要冻结的金额超出账户余额',FanweErrcode::SYSTEM_ERROR);
                        }

                        $diff_amount = $change_money - $user->vc_normal;
                        if($diff_amount>0)
                        {
                            $untrade_money = $diff_amount;
                            $normal_money = $user->vc_normal;
                        }
                        else
                        {
                            $normal_money = $change_money;
                        }

                        try{
                            DB::beginTransaction();
                            Helper::freezeCoin('manual',0,$normal_money,$untrade_money,$user->id,'后台冻结',$coin_type);
                            DB::commit();
                        }   catch (\Exception $e)
                        {
                            DB::rollback();
                            return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                        }

                    }
                    else
                    {
                        //非平台币
                        $user_asset = UserAsset::where(["user_id"=>$user->id,"coin_type"=>$coin_type])->first();
                        if($change_money>$user_asset->vc_total){
                            return $this->error('要冻结的金额超出账户余额',FanweErrcode::SYSTEM_ERROR);
                        }

                        try{
                            DB::beginTransaction();
                            Helper::freezeCoin('manual',0,0,$change_money,$user->id,'后台冻结',$coin_type);
                            DB::commit();
                        }   catch (\Exception $e)
                        {
                            DB::rollback();
                            return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                        }
                    }

                }
            break;
            case 2://充值

                if($coin_type==0&&config("app.otc")){
                    $vc_amount = $normal_money + $untrade_money;
                }
                else
                {
                    $vc_amount = $change_money;
                    $untrade_money = $change_money;
                    $normal_money = 0;
                }


                try{
                    DB::beginTransaction();
                    $allot_log_id = DB::table('allot_log')->insertGetId([
                        'to'=>$user->id,
                        'vc_amount'=>$vc_amount,
                        'vc_normal' => $normal_money,
                        'vc_untrade' => $untrade_money,
                        'create_time'=>date("Y-m-d H:i:s"),
                        'coin_type'=>$coin_type
                    ]);
                    Helper::incomeCoin('allot',$allot_log_id);
                    DB::commit();
                }   catch (\Exception $e)
                {
                    DB::rollback();
                    return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                }
                break;
            case 3: //手动扣减
                if($coin_type==0&&config("app.otc")){
                    $vc_amount = $normal_money + $untrade_money;
                    if($normal_money>$user->vc_normal){
                        return $this->error('扣减的金额超出用户的可交易余额',FanweErrcode::SYSTEM_ERROR);
                    }
                    if($untrade_money>$user->vc_untrade){
                        return $this->error('扣减的金额超出用户的不可交易余额',FanweErrcode::SYSTEM_ERROR);
                    }
                }
                else
                {
                    //非平台币
                    $user_asset = UserAsset::where(["user_id"=>$user->id,"coin_type"=>$coin_type])->first();
                    if($change_money>$user_asset->vc_total){
                        return $this->error('扣减的金额超出用户的账户余额',FanweErrcode::SYSTEM_ERROR);
                    }
                    $vc_amount = $change_money;
                    $untrade_money = $change_money;
                    $normal_money = 0;
                }


                try{
                    DB::beginTransaction();
                    $allot_log_id = DB::table('allot_expend_log')->insertGetId([
                        'user_id'=>$user->id,
                        'vc_amount'=>$vc_amount,
                        'vc_normal' => $normal_money,
                        'vc_untrade' => $untrade_money,
                        'create_time'=>date("Y-m-d H:i:s"),
                        'coin_type'=>$coin_type
                    ]);
                    Helper::expendCoin('allot',$allot_log_id);
                    DB::commit();
                }   catch (\Exception $e)
                {
                    DB::rollback();
                    return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                }
                break;
        }

        return $this->success();
    }

    //扣减/增加算力
    public function cp(Request $request){
        $id = $request->input('id');
        $user = User::find($id);
        if(!$user){
            return response()->json(['msg'=>'无此用户','code'=>FanweErrcode::USER_NOT_EXSITS]);
        }
        $cp_amount = $request->input('cp_amount');
        $type = $request->input('type');
        switch ($type){
            case 1://扣减
                if($cp_amount>$user->cp_total){
                    return $this->error('要扣减的算力值超出剩余算力',FanweErrcode::SYSTEM_ERROR);
                }
                try{
                    DB::beginTransaction();
                    $user->cp_total -= $cp_amount;
                    $user->save();
                    DB::commit();
                }   catch (\Exception $e)
                {
                    DB::rollback();
                    return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                }
                break;
            case 2://增加
                try{
                    DB::beginTransaction();
                    Helper::GrantCp($user->id,1,$cp_amount,"platform");
                    DB::commit();
                }   catch (\Exception $e)
                {
                    DB::rollback();
                    return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
                }
        }

        return $this->success();
    }

    //资金明细
    public function detail(Request $request,$id){
        $coin_type = $request->input("coin_type");
        $table = $request->input('table','income_log');
        $type = $request->input('type','all');
        $user = User::find($id);
        if($coin_type>0)
        {
            $userAsset = UserAsset::where(["user_id"=>$user->id,"coin_type"=>$coin_type])->first();
            $user->vc_total = $userAsset->vc_total;
            $user->vc_freeze = $userAsset->vc_freeze;
        }
        if($table=='income_log'){
            $typeNameArr = [
                'mine'=>'挖矿',
                'transfer'=>'转账',
                'purchase'=>'购买',
                'allot'=>'划拨',
                'steal'=>'偷币',
                'incharge'=>'充值'
            ];
            $lists = IncomeLog::where(function ($query) use($id,$type,$coin_type){
                $query->where('user_id',$id);
                if($type!=='all'){
                    $query->where('type',$type);
                }
                if($coin_type>0)
                {
                    $query->where("coin_type",$coin_type);
                }
                else
                {
                    $query->where("coin_type",0);
                }
            })->orderBy('id','desc')->paginate(20);
            $lists->map(function ($val) use($typeNameArr){
                $val->typeName = $typeNameArr[$val->type];
            });
        }elseif($table=='expend_log'){
            $typeNameArr = [
                'withdraw'=>'提现',
                'transfer'=>'转账',
                'sale'=>'销售',
                'exchange'=>'兑换'
            ];
            $lists = ExpendLog::where(function ($query) use($id,$type,$coin_type){
                $query->where('user_id',$id);
                if($type!=='all'){
                    $query->where('type',$type);
                }
                if($coin_type>0)
                {
                    $query->where("coin_type",$coin_type);
                }
                else
                {
                    $query->where("coin_type",0);
                }
            })->orderBy('id','desc')->paginate(20);
            $lists->map(function ($val) use($typeNameArr){
                $val->typeName = $typeNameArr[$val->type];
            });
        }elseif($table=='freeze_log'){
            $typeNameArr = [
                'withdraw'=>'提现',
                'manual'=>'平台',
                'sale'=>'挂卖'
            ];
            $lists = FreezeLog::where(function ($query) use($id,$type,$coin_type){
                $query->where('user_id',$id);
                if($type!=='all'){
                    $query->where('type',$type);
                }
                if($coin_type>0)
                {
                    $query->where("coin_type",$coin_type);
                }
                else
                {
                    $query->where("coin_type",0);
                }
            })->orderBy('id','desc')->paginate(20);
            $lists->map(function ($val) use($typeNameArr){
                $val->typeName = $typeNameArr[$val->type];
            });
        }
        $coin_list = Helper::getCoinType();
        return view("manage.user.detail",['lists'=>$lists,'table'=>$table,'type'=>$type,'user'=>$user,'coin_list'=>$coin_list,'coin_type'=>$coin_type]);
    }

    //流量主申请列表
    public function flow(Request $request)
    {
        $keyword = trim($request->input("keyword"));
        $date = $request->input('date');
        $time_fields = 'create_time';

        if($date)
        {
            $dateStr = explode('~',$date);
            $begin_date = $dateStr[0];
            $end_date = $dateStr[1];
            $param['date'] = $begin_date.'~'.$end_date;
        }
        if($keyword){
            $searchUid = DB::table('user')->whereRaw("username like'%".$keyword."%' or fanwe_user.mobile like '".$keyword."%'")->lists('id');
            $lists = DB::table('flow_apply')
                ->whereIn('user_id',$searchUid)
                ->where(function ($query) use($begin_date,$end_date,$time_fields){
                    if($begin_date)
                    {
                        $query->where($time_fields,">=",$begin_date." 00:00:00");
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<=",$end_date." 23:59:59");
                    }
                })
                ->orderBy('id','desc')
                ->paginate(20);
        }else{
            $lists = DB::table('flow_apply')
                ->where(function ($query) use($begin_date,$end_date,$time_fields){
                    if($begin_date)
                    {
                        $query->where($time_fields,">=",$begin_date." 00:00:00");
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<=",$end_date." 23:59:59");
                    }
                })
                ->orderBy('id','desc')
                ->paginate(20);
        }

        $uidArr = $lists->pluck('user_id')->unique();
        $userInfo = DB::table('user')->select('username','mobile','id')->whereIn('id',$uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');

        $lists->map(function ($list) use ($userInfo){
            $list->username = collect($userInfo->get($list->user_id))->get('username');
            $list->mobile = collect($userInfo->get($list->user_id))->get('mobile');
        });
        return view("manage.user.flow",['lists'=>$lists]);
    }

    //流量主审核
    public function flow_appeal(Request $request)
    {
        $id_arr = $request->input('id_arr');
        if(!$id_arr){
            return $this->error('参数错误');
        }
        $status = $request->status;
        if(!$status){
            return $this->error('参数错误');
        }
        $memo = $request->status;

        try{
            DB::beginTransaction();
            foreach ($id_arr as $key=>$val){
                $appeal_flow = FlowApply::find($val);
                if(!$appeal_flow){
                    throw new \Exception('not find appeal_flow');
                }
                $appeal_flow->status = $status;
                $appeal_flow->memo = $memo;
                $user = User::find($appeal_flow->user_id);
                if($status==2){
                    $user->flow_status = 2;
                }else{
                    $user->flow_status = 1;
                }

                $user->save();
                $appeal_flow->save();
            }
            DB::commit();
        }   catch (\Exception $e)
        {
            DB::rollback();
            return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
        }
        return $this->success();

    }

    //用户业绩查看
    public function achievement(Request $request,$id)
    {
        if(!$id){
            return $this->error();
        }
        $date = $request->input('date');
        if($date){
            $dateStr = explode('~',$date);
            $begin_date = $dateStr[0];
            $end_date = $dateStr[1];
            $param['date'] = $begin_date.'~'.$end_date;
        }
        $lists = PromoterReward::with('invite_user')->
        where(function($query) use($id,$begin_date,$end_date){
            $query->where('user_id',$id);
            if($begin_date)
            {
                $query->where('create_time',">=",$begin_date." 00:00:00");
            }
            if($end_date)
            {
                $query->where('create_time',"<=",$end_date." 23:59:59");
            }
        })->orderBy('id','desc')
        ->paginate(20);
        $sum = PromoterReward::where('user_id',$id)->sum('total_amount');
        if($date){
            $curr_sum = PromoterReward::where(function($query) use($id,$begin_date,$end_date){
                $query->where('user_id',$id);
                if($begin_date)
                {
                    $query->where('create_time',">=",$begin_date." 00:00:00");
                }
                if($end_date)
                {
                    $query->where('create_time',"<=",$end_date." 23:59:59");
                }
            })->sum('total_amount');
        }
        return view("manage.user.achievement",['date'=>$date,'lists'=>$lists,'sum'=>$sum,'curr_sum'=>$curr_sum]);
    }

    public function asset(Request $request)
    {
        $mobile = trim($request->input('mobile'));
        $coin_type = $request->input('coin_type');
        $user = User::where('mobile',$mobile)->first();
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        if($coin_type==0){
            $data = ['user_id'=>$user->id,'mobile'=>$user->mobile,'vc_total'=>$user->vc_total];
        }else{
            $asset = UserAsset::where('user_id',$user->id)->where('coin_type',$coin_type)->first();
            $data = ['user_id'=>$user->id,'mobile'=>$user->mobile,'vc_total'=>Helper::trimNumber($asset->vc_total,5)];
        }
        return $this->success('',$data);
    }

    //OTC授权
    public function otcauth(Request $request){
        $param = $request->all();
        $user = User::find($param['user_id']);
        if(!$user){
            return parent::error('无此用户',FanweErrcode::USER_NOT_EXSITS);
        }
        $user->otc_auth = $param['otc_auth'];
        if($user->otc_auth==0)
        {
            $user->otc_auth_type = 0;
            $user->limit_day = 0;
        }
        else
        {
            $user->otc_auth_type = $param['otc_auth_type'];
            if($param['otc_auth_type']>0&&$param['otc_auth_type']<3)
            {
                if(!$param['limit_day'])
                {
                    return $this->error('请输入正确的锁定天数');
                }
                $user->limit_day = $param['limit_day'];
            }else
            {
                $user->limit_day = 0;
            }
        }
        $result = $user->save();
        if($result)
        {
            return $this->success();
        }
        else
        {
            return $this->error();
        }
    }

}
