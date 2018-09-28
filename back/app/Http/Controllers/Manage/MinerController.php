<?php

namespace App\Http\Controllers\Manage;

use App\Http\Models\MinerUserLog;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MinerController extends AuthBaseController
{
    public function index(){
        return view('manage.miner.index');
    }

    public function getList(Request $request){
        $post = $request->all();
        $data = DB::table('miner as a')->leftJoin("coin_type as b","a.coin_type",'=','b.id')->select('a.id','a.name','a.price','a.coin_type','a.expire_time','a.compute_power','a.status','a.stock','a.high','a.low','a.change_time','a.float_per_before','a.float_per_after','a.agent_cp',"b.coin_unit")
            ->where(function ($query) use($post){
                if($post['status'] != ''){
                    $query->where('a.status',$post['status']);
                }
            })
            ->paginate(10);
        $data_list = $data->toArray();
        array_walk($data_list['data'],function (&$v){
            if($v->coin_type == 0){
                $platcoin = (array)DB::table("conf")->select("keyvalue")->find(1);
                $v->coin_unit = $platcoin["keyvalue"];
            }
            $v->expire_time = floor($v->expire_time/86400);
            $v->change_time = floor($v->change_time/86400);
        });

        $res['list'] = $data_list['data'];
        $res['total'] = $data_list['total'];
        $res['pagehtml'] = $data->render();
        $res['test'] = $post;
        return $res;
    }
    public function getCointype(){
        $data_list = (array)DB::table("coin_type")->select('id','coin_unit')->where("status",1)->get();
        $platcoin  = (array)DB::table("conf")->select("keyvalue")->find(1);
        $data = ['id'=>0,"coin_unit"=>$platcoin["keyvalue"]];
        array_unshift($data_list,$data);
        return $data_list;
    }
    public function lock(Request $request){
        $post = $request->all();
        $res = DB::table('miner')->where('id',$post['miner_id'])->update(['status'=>$post['status']]);
        if(!$res){
            return $this->error('发生错误');
        }
        return $this->success();
    }
    public function getById(Request $request){
        $id = $request->input('miner_id');
        $data = DB::table('miner')->find($id);
        $data->expire_time = floor($data->expire_time/86400);
        $data->change_time = floor($data->change_time/86400);
        return $this->success('操作成功',$data);
    }
    public function pop(Request $request){
        $post = $request->all();
        if(!$post['name']){
            return $this->error('请输入矿机名');
        }
        if(!$post['compute_power']){
            return $this->error('请输入算力');
        }
        if(!$post['price']){
            return $this->error('请输入价格');
        }
        if(!$post['expire_time']){
            return $this->error('请输入运行天数');
        }
        if(!$post['change_time']){
            return $this->error('请输入变化天数');
        }
        if($post['stock'] == ''){
            return $this->error('请输入库存');
        }
        if($post['chang_time']>$post['expire_time']){
            return $this->error('变化天数不能大于运行天数');
        }
        if($post['high'] == ''){
            return $this->error('请输入日产量最高值');
        }
        if((!$post['coin_type'])&&$post["coin_type"]!=0){
            return $this->error('请输入币种类型');
        }
        if((!$post['agent_cp'])&&$post["agent_cp"]>=0){
            return $this->error('请输入正确的返还算力');
        }
        if($post['low'] == ''){
            return $this->error('请输入日产量最低值');
        }
        if($post["low"]>$post["high"]){
            return $this->error('日产量范围错误');
        }
        if($post['float_per_before'] == ''){
            return $this->error('请输入变化前收益率');
        }

        if($post['float_per_after'] == ''){
            return $this->error('请输入变化后收益率');
        }
        if($post['float_per_after']>1||$post['float_per_after']<0||$post['float_per_after']>1||$post['float_per_after']<0){
            return $this->error('收益率范围输入错误');
        }
        $post['expire_time'] *=86400;
        $post['change_time'] *=86400;

        if(!$post['miner_id']){//添加
            $res = DB::table('miner')->insert(['name'=>$post['name'],'compute_power'=>$post['compute_power'],'coin_type'=>$post['coin_type'],'price'=>$post['price'],'expire_time'=>$post['expire_time'],'stock'=>$post['stock'],'high'=>$post['high'],'low'=>$post['low'],'change_time'=>$post['change_time'],'float_per_before'=>$post['float_per_before'],'float_per_after'=>$post['float_per_after'],'agent_cp'=>$post['agent_cp']]);
        }else{//修改
            $res = DB::table('miner')->where('id',$post['miner_id'])->update(['name'=>$post['name'],'coin_type'=>$post['coin_type'],'compute_power'=>$post['compute_power'],'price'=>$post['price'],'expire_time'=>$post['expire_time'],'stock'=>$post['stock'],'high'=>$post['high'],'low'=>$post['low'],'change_time'=>$post['change_time'],'float_per_before'=>$post['float_per_before'],'float_per_after'=>$post['float_per_after'],'agent_cp'=>$post['agent_cp']]);
        }
        if($res !== false){
            return $this->success();
        }else{
            return $this->error('操作失败');
        }

    }


    public function salelist(Request $request){
        $param = $request->all();
        $param['status'] = isset($param['status'])?$param['status']:-1;
        $list = MinerUserLog::leftJoin('user as u','u.id','=','mu.user_id')
            ->leftJoin('miner as m','m.id','=','mu.miner_id')
            ->select('mu.*','u.username','m.name','m.price','m.compute_power')->where(function ($query)use($param){
                if($param["status"]>=0)
                {
                    $query->where("mu.status",$param["status"]);
                }
                if($param['username'])
                {
                    $query->whereRaw("fanwe_u.username like'%".$param["username"]."%' or fanwe_u.mobile like '".$param["username"]."%'");
                }
                if($param['date'])
                {
                    $dateStr = explode('~',$param['date']);
                    $date_s = strtotime($dateStr[0]);
                    $date_e = strtotime($dateStr[1]);
                    if($date_s)
                    {
                        $query->where('mu.create_time',">=",$date_s);
                    }
                    if($date_e)
                    {
                        $query->where('mu.create_time',"<=",$date_e);
                    }
                }
            })->paginate(10);
        foreach ($list->items() as &$v) {
            $v->create_time = date('Y-m-d H:i:s',$v->create_time);
            $v->expire_time = date('Y-m-d H:i:s',$v->expire_time);
        }
        //统计
        $data = [];
        $time_today = strtotime(date('Y-m-d',time()));
        $data['num_all'] = MinerUserLog::count();
        $data['num_today'] = MinerUserLog::where('create_time','>=',$time_today)->where('create_time','<',$time_today+86400)->count();
        $data['amount_all'] = MinerUserLog::leftJoin('miner as m','m.id','=','mu.miner_id')->sum('m.price');
        $data['amount_today'] = MinerUserLog::leftJoin('miner as m','m.id','=','mu.miner_id')->where('create_time','>=',$time_today)->where('create_time','<',$time_today+86400)->sum('m.price');
        return view('manage.miner.sale',['list'=>$list,'param'=>$param,'data'=>$data]);
    }

    //保存说明
    public function savedescribe(Request $request)
    {
        $describe = $request->input("describe");
        $miner_id = $request->input("miner_id");
        $res = DB::table("miner")->where("id",$miner_id)->update(["describe"=>$describe]);
        if($res)
        {
            return $this->success();
        }
        else
        {
            return $this->error();
        }
    }
}