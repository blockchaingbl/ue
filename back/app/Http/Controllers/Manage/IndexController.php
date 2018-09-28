<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\BlockSynchronize;
use App\Http\Models\EthereumAccount;
use App\Http\Models\Manage\Admin;
use App\Http\Models\MingrenTokenTransaction;
use App\Http\Models\OtcOrder;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
include_once(base_path("app/Library/Ethereum/ethereum.php"));
class IndexController extends AuthBaseController
{

    //主页
    public function index(Request $request)
    {

        if($request->input("sync_block")==1){
            //输出区块信息
            $ethereum_block_datas = BlockSynchronize::all();
            $chain_config  = config("app.block_chain");
            foreach($ethereum_block_datas as $k=>$ethereum_block_data){
                $ethereum_block_data = $ethereum_block_data->toArray();
                $chain = $chain_config[$ethereum_block_data['block_chain']];
                $eth = new \Ethereum($chain['rpc_server_host'], $chain['rpc_server_port']);
                $eth->change_chain($ethereum_block_data['block_chain']);
                $ethereum_block_data['latest_block_number'] = $eth->eth_blockNumber(true);
                $ethereum_block_datas[$k] = $ethereum_block_data;
            }

            return $ethereum_block_datas;
        }

        //用户统计
        $user_today = User::whereRaw("DATE_FORMAT(create_time,'%Y-%m-%d')='".date("Y-m-d",time())."'")->count();
        $stat["user_today"] = $user_today; //今日注册数
        $user_total = User::count();
        $stat["user_total"] = $user_total; //总注册数

        $user_chart_date = $request->input('user_chart_date');
        if($user_chart_date)
        {
            $userDateStr = explode('~',$user_chart_date);
            $user_begin_date = $userDateStr[0];
            $user_end_date = $userDateStr[1];
            $param['user_chart_date'] = $user_begin_date.'~'.$user_end_date;
        }
        $user_chart = User::select(
            DB::raw("count(*) as num"),
            DB::raw("DATE_FORMAT(create_time,'%Y-%m-%d') as date"))
            ->where(function($query) use($user_begin_date,$user_end_date){
                if($user_begin_date)
                {
                    $query->where("create_time",">",$user_begin_date." 00:00:00");
                }
                if($user_end_date)
                {
                    $query->where("create_time","<",$user_end_date." 23:59:59");
                }
            })
            ->groupBy("date")
            ->orderBy("date","desc")
            ->limit(7)
            ->get()->toArray();
        krsort($user_chart);
        $stat["user_chart"] = $user_chart; //图表数据

        $sale_chart_date = $request->input('sale_chart_date');
        if($sale_chart_date){
            $saleDateStr = explode('~',$sale_chart_date);
            $sale_begin_date = $saleDateStr[0];
            $sale_end_date = $saleDateStr[1];
            $param['sale_chart_date'] = $sale_begin_date.'~'.$sale_end_date;
        }
        $otc_chart = OtcOrder::select(
            DB::raw('sum(vc_total_price) as total_price,count(*) as num'),
            DB::raw("FROM_UNIXTIME(create_time,'%Y-%m-%d') as date"))
            ->where(function($query) use($sale_begin_date,$sale_end_date){
                if($sale_begin_date)
                {
                    $query->where("create_time",">",strtotime($sale_begin_date." 00:00:00"));
                }
                if($sale_end_date)
                {
                    $query->where("create_time","<",strtotime($sale_end_date." 23:59:59"));
                }
            })
            ->groupBy('date')
            ->orderBy("date","desc")
            ->limit(7)
            ->get()->toArray();
        krsort($otc_chart);
        $stat['otc_chart'] = $otc_chart;
        //订单统计
        $stat['otc_order_total'] = OtcOrder::all()->count();
        $stat['otc_order_today'] = OtcOrder::whereRaw('create_time >'.strtotime('today').' AND create_time<'.strtotime('today +1day'))->count();
        $stat['otc_order_sum'] = OtcOrder::where('status',2)->sum('vc_total_price');
        $stat['otc_sum_today'] = OtcOrder::whereRaw('create_time >'.strtotime('today').' AND create_time<'.strtotime('today +1day'))->sum('vc_total_price');

        return view("manage.home",["stat"=>$stat,"param"=>$param]);
    }

    public function repairblock(){
        $eth = new \Ethereum(config("app.rpc_server_host"), config("app.rpc_server_port"));
        $latest_block_number = $eth->eth_blockNumber(true);
        if($latest_block_number){
            $ethereum_block_data = BlockSynchronize::where("block_chain","ethereum")->first();
            $ethereum_block_data->repair_block_number = $ethereum_block_data->block_number; //从当前区块开始修复
            $ethereum_block_data->block_number = $latest_block_number;  //当前区块从最新开始
            $ethereum_block_data->end_block_number =  $latest_block_number;  //修复到任务开始时的这个最新区块为止
            $ethereum_block_data->save();
            DB::table("ethereum_account")->truncate();
            return $this->success("任务开启");
        }
        else
        {
            return $this->error("获取最新区块数据失败，请重试");
        }
    }

    //更改密码
    public function to_update_mypwd()
    {
        return view("manage.admin.updatemypwd");
    }

    //更改密码操作
    public function update_my_pwd(Request $request)
    {
        if(!$request->old_password){
            return redirect()->back()->withErrors($this->error("请输入原密码",FanweErrcode::MANAGE_OLDPASSWORD_NOT_EXIST));
        }
        if(!$request->new_password){
            return redirect()->back()->withErrors($this->error("请输入新密码",FanweErrcode::MANAGE_NEWPASSWORD_NOT_EXIST));
        }
        if(!$request->check_password){
            return redirect()->back()->withErrors($this->error("请输入确认密码",FanweErrcode::MANAGE_CHECKPASSWORD_NOT_EXIST));
        }
        if($request->new_password != $request->check_password){
            return redirect()->back()->withErrors($this->error("两次输入的密码不一致",FanweErrcode::MANAGE_ADMINPWDCHECK_ERROR));
        }
        $admin = Auth::user();
        $password = Admin::where(['id'=>$admin->id])->first()->password;
        if(!Hash::check($request->old_password,$password)){
            $return['errcode'] = 10000;
            $return['message'] = "";
            return redirect()->back()->withErrors($this->error("原密码错误",FanweErrcode::MANAGE_OLDPASSWORD_ERROR));
        }else{
            $param = [
                'id'=>$admin->id,
                'password'=>$request->new_password
            ];
            $res = Admin::updateAdminPwd($param);
            if($res){
                Auth::logout();
                return redirect('/');
            }else{
                return redirect()->back()->withErrors($this->error());
            }
        }
    }

}
