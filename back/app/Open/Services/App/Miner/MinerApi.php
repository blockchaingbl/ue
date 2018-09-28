<?php
namespace App\Open\Services\App\Miner;

use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\MinerMineLog;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Web\User;
use App\Http\Models\MinerUserLog;

class MinerApi extends FanweAuthService
{
    /**
     * @name get_list
     * @description 获取矿机列表
     * @param
     * page：页数
     * @return market_lists
     * {
    "data": {
    "miner_list": [
    {
    "id": 1,
    "name": "1星矿机",
    "price": 1000,
    "expire_time": "2018-07-27 16:38:07",
    "compute_power": "10.00000000",
    "stock": 9999
    }
    ]
    }
     */
    public function get_list($param){
        $data = DB::table('miner as a')->leftJoin("coin_type as b","b.id","=","a.coin_type")->select('a.id','a.name','a.price','a.expire_time','a.compute_power','a.stock','a.high','a.low','a.coin_type','a.status','b.coin_unit')->get();
        array_walk($data,function (&$v){
            if($v->coin_type == 0){
                $platcoin = (array)DB::table("conf")->select("keyvalue")->find(1);
                $v->coin_unit = $platcoin["keyvalue"];
            }
            $v->expire_time = floor($v->expire_time/86400);
            $v->compute_power = sprintf("%.3f",$v->compute_power);
        });
        $this->setData('miner_list',$data);
        return $this->success();
    }
    /**
     * @name miner_buy
     * @description 购买矿机
     * @param
     * miner_id：矿机id
     * @return
     * [
     * ]
     */
    public function miner_buy($param){
        $user = $this->user;
        $miner = (array)DB::table('miner')->where('status',1)->find($param['miner_id']);
        if(!count($miner)){
            return $this->error('参数错误:miner_id:'.$miner['id']);
        }

//        $miner_user_log = MinerUserLog::where(["user_id"=>$user->id,"miner_id"=>$param['miner_id']])
//            ->where('expire_time','>',time())->first();
//        if($miner_user_log)
//        {
//            return $this->error('您购买的该矿机还未到期，不允许再次购买');
//        }

        //判断库存
        if($miner['stock']<=0){
            return $this->error('库存不足');
        }
        //判断余额，错误返回
        if($this->user->vc_total < $miner['price']){
            return $this->error('余额不足');
        }
        //成功购买，增加用户矿机表，更新用户信息，增加支出记录，增加算力，减库存

        DB::beginTransaction();
        try{
            $mu_id = DB::table('miner_user_log')->insertGetId(['user_id'=>$this->user->id,'coin_type'=>$miner["coin_type"],'miner_id'=>$param['miner_id'],'cp_total'=>$miner['compute_power'],'create_time'=>time(),'expire_time'=>time()+$miner['expire_time'],'change_time'=>time()+$miner['change_time'],'high'=>$miner['high'],'low'=>$miner['low'],'float_per_before'=>$miner['float_per_before'],'float_per_after'=>$miner['float_per_after'],'agent_cp'=>$miner['agent_cp']]);//ok
            Helper::expendCoin('miner_buy',$mu_id);

//            DB::table('user')->where('id',$this->user->id)->update(['vc_total'=>$vc_untrade+$vc_normal,'vc_normal'=>$vc_normal,'vc_untrade'=>$vc_untrade]);
//            $detail = '购买矿机消费'.$miner['price'];
//            DB::table('expend_log')->insert(['user_id'=>$this->user->id,'coin_type'=>0,'detail'=>$detail,'create_time'=>time(),'type'=>'miner_buy','vc_normal'=>$vc_normal_ex,'vc_untrade'=>$vc_untrade_ex,'vc_amount'=>$miner['price'],'relate'=>$mu_id]);

            DB::table('miner')->where('id',$miner['id'])->update(['stock'=>$miner['stock']-1]);
            if($miner['agent_cp']>0){
                $invite_user_id = User::select("pid")->find($this->user->id);

                if($invite_user_id->pid>0){
                    Helper::GrantCp($invite_user_id->pid,1,$miner['agent_cp'],'miner');
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $this->error($e->getMessage());
        }

        return $this->success();
    }
    /**
     * @name miner_info
     * @description 矿机详情
     * @param
     * miner_id：矿机id
     * @return miner_info
     *"miner_info": {
    "id": 7,
    "name": "新矿机",
    "price": 10,
    "expire_time": 86400,
    "detail": null,
    "compute_power": "10.00000000",
    "duration": null,
    "status": "1",
    "stock": 0
    }
     */
    public function miner_info($param){
        $data = (array)DB::table('miner')->where('status',1)->find($param['miner_id']);
        if(!count($data)){
            return $this->error('参数错误');
        }
        if($data["coin_type"] == 0){
            $platcoin = (array)DB::table("conf")->select("keyvalue")->find(1);
            $unit["coin_unit"] = $platcoin["keyvalue"];
        }else{
            $unit = (array)DB::table("coin_type")->find($data['coin_type']);
        }

        $data['expire_time'] = floor($data['expire_time']/86400);
        $data["coin_unit"]=$unit["coin_unit"];
        $data['compute_power'] = sprintf("%.3f",$data['compute_power']);
//        $data['describe'] = db_config("MINER_DESCRIBE");
        $this->setData('miner_info',$data);
        return $this->success();
    }
    /**
     * @name miner_user_list
     * @description 我的矿机列表
     * @param
     * @return list
     * {
    "id": 4,
    "user_id": 145,
    "miner_id": 4,
    "create_time": 1531731815,
    "expire_time": 864000,
    "status": "1",
    "name": "4星矿机",
    "price": 10,
    "detail": null,
    "compute_power": "10.00000000",
    "duration": 3600,
    "stock": 9999
    }
     */
    public function miner_user_list($param){
        $data = MinerUserLog::getListByUid($this->user->id)->toArray();
        array_walk($data,function (&$v){
            $remaining_time = strtotime(date('Y-m-d',$v['expire_time'])) -strtotime(date('Y-m-d',time()));
            if($v["coin_type"] == 0){
                $platcoin = (array)DB::table("conf")->select("keyvalue")->find(1);
                $unit["coin_unit"] = $platcoin["keyvalue"];
            }else{
                $unit = (array)DB::table("coin_type")->find($v['coin_type']);
            }
            $miner_mine_log = MinerMineLog::where(["user_id"=>$v['user_id'],"miner_id"=>$v['id'],"mined"=>1])->get();
            $income = 0;
            foreach($miner_mine_log as $item)
            {

                $income += $item->amount;

            }
            $v['income'] =round($income,2);
            $v["coin_unit"]= $unit["coin_unit"];
            $v['expire_time'] = floor($remaining_time/86400);
            if($remaining_time<=0){
                $v['status'] = 2;//过期
                $v['expire_time'] = 0;
            }
            $v['compute_power'] = sprintf("%.3f",$v['compute_power']);
        });
        $this->setData('list',$data);
        return $this->success();
    }
    public function my_miner_one($param){
//        $data = MinerUserLog::where("id",11)->first();
        return $param;
    }

    /**
     * @name expired_miner_list
     * @description 我的历史矿机列表
     * @param
     * @return list
     * {
    "id": 4,
    "user_id": 145,
    "miner_id": 4,
    "create_time": 1531731815,
    "expire_time": 864000,
    "status": "1",
    "name": "4星矿机",
    "price": 10,
    "detail": null,
    "compute_power": "10.00000000",
    "duration": 3600,
    "stock": 9999
    }
     */
    public function expired_miner_list($param){
        $data = MinerUserLog::getExpiredMinerListByUid($this->user->id)->toArray();
        array_walk($data,function (&$v){
            if($v["coin_type"] == 0){
                $platcoin = (array)DB::table("conf")->select("keyvalue")->find(1);
                $unit["coin_unit"] = $platcoin["keyvalue"];
            }else{
                $unit = (array)DB::table("coin_type")->find($v['coin_type']);
            }
            $v["cp_total"] = sprintf("%.3f",$v['cp_total']);;
            $miner_mine_log = MinerMineLog::where(["user_id"=>$v['user_id'],"miner_id"=>$v['id'],"mined"=>1])->get();
            $income = 0;
            foreach($miner_mine_log as $item)
            {
                $income += $item->amount;
            }
            $v['income'] =round($income,2);
            $v["coin_unit"]= $unit["coin_unit"];
            $v['expire_time'] = date('Y-m-d',$v['expire_time']);
            $v['compute_power'] = sprintf("%.3f",$v['compute_power']);
        });
        $this->setData('list',$data);
        return $this->success();
    }

    public function getminelog($param){
        $data1= (array)DB::table("mine_log")->select("mined_time","coin_type","amount")->where("user_id",$this->user->id)->where("mined",1)->orderBy("mined_time","desc")->limit(7)->get();
        $data2 = (array)DB::table("miner_mine_log")->select("mined_time","coin_type","amount")->where("user_id",$this->user->id)->where("mined",1)->orderBy("mined_time","desc")->limit(7)->get();
        $all_data = array_merge($data1,$data2);
        $key_arrays = [];
        foreach($all_data as $val){
            $key_arrays[]=$val->mined_time;
        }
        array_multisort($key_arrays,SORT_DESC,$all_data);
        $all_data = array_slice($all_data ,0 ,5);
        foreach ($all_data as $key=>$v){
            if($v->coin_type==0)
            {
                $v->coin_unit = db_config("COIN_UNIT");
            }
            else
            {
                $v->coin_unit = CoinType::where("id",$v->coin_type)->value("coin_unit");
            }
            $v->mined_time = date("Y-m-d H:i:s",$v->mined_time);
        }
        $this->setData('data',$all_data);
        return $this->success();
    }

    /**
     * @name user_miner_detail
     * @description 用户购买矿机详情
     * @param $param
     * user_miner_id：用户购买矿机记录ID
     * @return
     * un_mined
     * log
     */
    public function user_miner_detail($param){
        $user_miner_id = $param["user_miner_id"];
        $miner_user_log = DB::table("miner_user_log as a")->select('a.*','b.name','b.coin_type','b.id as mid')->leftJoin("miner as b","a.miner_id","=","b.id")->where("a.id",$user_miner_id)->first();
        if(!$miner_user_log)
        {
            return $this->error("无效的记录");
        }
        $remaining_time = strtotime(date('Y-m-d',$miner_user_log->expire_time)) - strtotime(date('Y-m-d',time()));
        $miner_user_log->cp_total = intval($miner_user_log->cp_total);
        if($miner_user_log->coin_type == 0){
            $platcoin = (array)DB::table("conf")->select("keyvalue")->find(1);
            $unit["coin_unit"] = $platcoin["keyvalue"];
        }else{
            $unit = (array)DB::table("coin_type")->find($miner_user_log->coin_type);
        }
        $miner_user_log->coin_unit = $unit['coin_unit'];
        $miner_user_log->expire_day = floor($remaining_time/86400);
        if($miner_user_log->expire_day<0){
            $miner_user_log->expire_day = 0;
        }
        $miner_user_log->expire_date = date("Y.m.d",$miner_user_log->expire_time);

        //计算累计收益，其他币都按照价格换算成平台币
        $miner_mine_log = MinerMineLog::where(["user_id"=>$miner_user_log->user_id,"miner_id"=>$user_miner_id,"mined"=>1])->get();
        $platform_price = db_config("COIN_PRICE");
        $income = 0;
        foreach($miner_mine_log as $item)
        {

            $income += $item->amount;

        }
        $miner_user_log->income =round($income,2);

        $this->setData("info",$miner_user_log);
        return $this->success();
    }

    /**
     * @name mined
     * @description 矿机挖矿收益记录
     * @param $param
     * user_miner_id：用户购买矿机记录ID
     * @return
     * un_mined
     * log
     */
    public function mined($param){
        $user = $this->user;
        $user_miner_id = $param["user_miner_id"];
        $miner_user_log = MinerUserLog::where("id",$user_miner_id)->first();
        if(!$miner_user_log)
        {
            return $this->error("无效的记录");
        }
        $miner_mine_log = MinerMineLog::where(["user_id"=>$user->id,"miner_id"=>$user_miner_id,"mined"=>1])->paginate(10);
        $miner_mine_log->map(function($item) use($miner_mine_log){
            if($item->coin_type==0)
            {
                $item->coin_type = db_config("COIN_UNIT");
            }
            else
            {
                $item->coin_type = CoinType::where("id",$item->coin_type)->value("coin_unit");
            }
            $item->create_time = date("Y.m.d",$item->create_time);
        });
        $miner_mine_log = $miner_mine_log->toArray()["data"];
        $this->setData("log",$miner_mine_log);
        return $this->success();
    }

    /**
     * @name fetch
     * @description 领取矿机挖出的收益
     * @param $param
     * user_miner_id：用户购买矿机记录ID
     * @return
     */
    public function fetch($param){
        $user_miner_id = $param["user_miner_id"];
        $miner_user_log = MinerUserLog::where("id",$user_miner_id)->first();
        if(!$miner_user_log)
        {
            return $this->error("无效的记录");
        }
        DB::beginTransaction();
        try{
            Helper::incomeCoin("miner_mine",$miner_user_log->id);
            $this->user->save();
            DB::commit();
            return $this->success();
        }catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }
}