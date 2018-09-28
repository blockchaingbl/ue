<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\CoinType;
use App\Http\Models\Manage\Admin;
use App\Http\Models\SubscribeToken;
use App\Http\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class WithdrawController extends AuthBaseController
{

    //提现管理
    public function index(Request $request)
    {
        $param = $request->all();
        $lists = DB::table('withdraw')->leftJoin('user','user.id','=','withdraw.user_id')
            ->select('withdraw.*','user.username','user.mobile')
            ->where(function ($query) use($param){
                if($param['coin_type']>0)
                {
                    $query->where("withdraw.coin_type",$param['coin_type']);
                }
                else
                {
                    $query->where("withdraw.coin_type",0);
                }
                if($param['username'])
                {
                    $query->whereRaw("fanwe_user.username like'%".$param["username"]."%' or fanwe_user.mobile like '".$param["username"]."%'");
                }
                if($param['withdraw_date'])
                {
                    $dateStr = explode('~',$param['withdraw_date']);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    if($param['time_type']==1){
                        $time_fields = 'withdraw.create_time';
                    }else{
                        $time_fields = 'withdraw.send_time';
                    }
                    if($begin_date)
                    {
                        $query->where($time_fields,">=",$begin_date." 00:00:00");
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<=",$end_date." 23:59:59");
                    }
                }
                if($param['status'])
                {
                    $status = $param['status'];
                    switch($status)
                    {
                        case 1:  //待审核
                            $query->where("withdraw.status",0);
                            break;
                        case 2: //待发放（已审核，未发送交易）
                            $query->where("withdraw.status",1)->where(function($query){
                                $query->where("withdraw.tx_hash","=","");
                            });
                            break;
                        case 3: //发放中
                            $query->where("withdraw.status",1)->where(function($query){
                                $query->where("withdraw.tx_hash","<>","");
                            });
                            break;
                        case 4: //发放成功
                            $query->where("withdraw.send_status","success");
                            break;
                        case 5: //发放失败
                            $query->where("withdraw.send_status","error");
                            break;
                        case 6: //拒绝
                            $query->where("withdraw.status",2);
                            break;

                    }
                }
            })
            ->orderBy('withdraw.id','desc')
            ->paginate(20);

        $coin_list = Helper::getCoinType();
        $platform_token = SubscribeToken::where("platform_coin",1)->first();
        return view("manage.withdraw.index",['lists'=>$lists,'param'=>$param,'coin_list'=>$coin_list,'platform_token'=>$platform_token]);
    }

    //提现审核
    public function examine(Request $request){
        $id = $request->input('id');
        $type = $request->input('status');
        $check = Withdraw::find($id);
        if($check->status!==0){
            return parent::error('只有待审核可以操作',FanweErrcode::SYSTEM_ERROR);
        }
        if($type==1){//通过提现
            try{
                DB::beginTransaction();
                Withdraw::where('id',$id)->update(array_merge($request->only('memo','status'),['confirm_time'=>date('Y-m-d H:i:s')]));
                Helper::expendCoin('withdraw',$id);
                DB::commit();
                return $this->success();
            }   catch (\Exception $e)
            {
                DB::rollback();
                return $this->error('操作失败,请确认用户剩余冻结金额',FanweErrcode::SYSTEM_ERROR);
            }
        }else if($type==2){//拒绝提现
            try{
                DB::beginTransaction();
                Withdraw::where('id',$id)->update($request->only('memo','status'));
                $relate  = DB::table('freeze_log')->where(['type'=>'withdraw','relate'=>$id])->first();
                Helper::freeCoin($relate->id);
                DB::commit();
                return $this->success();
            }   catch (\Exception $e)
            {
                DB::rollback();
                return $this->error();
            }
        }else{
            return $this->error();
        }
    }

    //转账数据导出
    public function export(Request $request){
        $coin_type = $request->input("coin_type");
        $ids = $request->input("ids");
        $ids = explode(',',$ids);
        if($coin_type==0)
        {
            $coin_config['name'] = '平台币';
            $coin_config['coin_unit'] = db_config('COIN_UNIT');
            $coin_config['withdraw_open'] = db_config('WITHDRAW_OPEN');
        }
        else
        {
            $coin_config = CoinType::where("id",$coin_type)->first()->toArray();
        }
        if(!$coin_config)
        {
            return $this->error('币不存在',FanweErrcode::SYSTEM_ERROR);
        }
        if(!$coin_config['withdraw_open'])
        {
            return $this->error($coin_config['name'].'('.$coin_config['coin_unit'].')暂不支持导出做转账操作',FanweErrcode::SYSTEM_ERROR);
        }
        if(!$ids)
        {
            return $this->error('请选择需要导出的记录',FanweErrcode::SYSTEM_ERROR);
        }
        $list = Withdraw::where(["coin_type"=>$coin_type,"status"=>1])->whereIn("id",$ids)->get();
        $cellData = [['提现币种:'.$coin_config['name'],'','','',''],['提现地址','提现金额','提现手续费','应打款']];
        foreach($list as $item)
        {
            $cellData[] = [$item->to_address,$item->vc_amount,$item->withdraw_fee,$item->vc_amount - $item->withdraw_fee];
        }
        try{
             Excel::create('提现转账记录',function($excel) use ($cellData,$coin_type,$ids){
                $excel->sheet('score', function($sheet) use ($cellData){
                    $sheet->rows($cellData);
                });
                Withdraw::where(["coin_type"=>$coin_type,"status"=>1])->whereIn("id",$ids)->increment("export_num",1);
            })->export('xls');
            return $this->success();
        }catch(\Exception $e){
            Log::warn($e->getMessage());
        }
    }


}
