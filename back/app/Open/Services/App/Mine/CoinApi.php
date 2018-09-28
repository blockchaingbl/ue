<?php
namespace App\Open\Services\App\Mine;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\MineIncharge;
use App\Http\Models\MineLog;
use App\Http\Models\MinePool;
use App\Http\Models\MinerMineLog;
use App\Http\Models\StealLog;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\MinerUserLog;

class CoinApi extends FanweAuthService
{
    private $max_hours = 48;  //最长挖矿时间，48小时
    private $min_minute = 10;  //最小挖矿时间 10分钟，即10分钟后才会产出

    //计算矿机挖矿
    private function miner_mine($user){
        DB::beginTransaction();
        try{
            $now = time();
            //获取矿机列表
            $miner_types = MinerUserLog::getMinerListByUid($user->id);
            foreach($miner_types as $row)
            {
                //当天应生成的矿
                $random_num = rand(0,100)/100;
                $middle = round(($row->high+$row->low)/2);
                if($now>$row->change_time){
                    if($row->float_per_before>$random_num){
                        $fvc = rand($row->low,$middle)/8;
                    }else{
                        $fvc = rand($middle,$row->high)/8;
                    }
                }else{
                    if($row->float_per_after>$random_num){
                        $fvc = rand($row->low,$middle)/8;
                    }else{
                        $fvc = rand($middle,$row->high)/8;
                    }
                }

                if($fvc<=0)$fvc=0;
                $unmined_list = MinerMineLog::where(["user_id"=>$user->id,"mined"=>0,"coin_type"=>$row->coin_type,"miner_id"=>$row->id])->orderBy("create_time")->get();
                $all_mine_list = MinerMineLog::where(["user_id"=>$user->id,"coin_type"=>$row->coin_type,"miner_id"=>$row->id])->orderBy("create_time","desc")->get();
                //当存在未领取的矿最小时间间隔小于3小时，或最大时间间隔大于48小时，不再生成新的矿
                if($unmined_list[0]->create_time == null){
                    $condition_1 = true;
                }else{
                    $condition_1 = ($now-$unmined_list[0]->create_time)<172800;
                }
                if((($condition_1&&$now-$all_mine_list[0]->create_time>10800))||count($all_mine_list) == 0)
                {
                    $miner_mine_log = new MinerMineLog();
                    $miner_mine_log->user_id = $user->id;
                    $miner_mine_log->create_time = $now;
                    $miner_mine_log->coin_type = $row->coin_type;
                    $miner_mine_log->miner_id = $row->id;
                    $miner_mine_log->amount = $fvc;
                    $miner_mine_log->mined = 0;
                    $miner_mine_log->save();
                }
            }
            DB::commit();
        }catch(\Exception $e)
        {
            DB::rollback();
        }
    }

    //计算指定用户的矿
    private function compute_mine($user)
    {
        DB::beginTransaction();
        $cp = $user->cp_total ; //(个人算力+个人购买的矿机算力)
        $cp_total = User::sum("cp_total"); //(所有用户总算力+所有矿机总算力)
        try{
            $now = time();
            $max_hours = $this->max_hours; //最多存储48小时

            $coin_types = CoinType::where("status",1)->get();
            $coin_type_configs = [];
            $coin_type_configs[0] = ["coin_unit"=>db_config("COIN_UNIT"),"mine_percent"=>db_config("MINE_PERCENT"),"mine_peroid"=>db_config("MINE_PEROID"),"mine_div"=>db_config("MINE_DIV"),"mine_min"=>db_config("MINE_MIN")];
            foreach($coin_types as $row)
            {
                $data = ["coin_unit"=>$row->coin_unit,"mine_percent"=>$row->mine_percent,"mine_peroid"=>$row->mine_peroid,"mine_div"=>$row->mine_div,"mine_min"=>$row->mine_min];
                $coin_type_configs[$row->id] = $data;
            }
            $mined_time = MineLog::where(["user_id"=>$user->id,"mined"=>1])->max("mined_time");  //最后装满的时间也表示最后挖出的时间

            foreach($coin_type_configs as $coin_id=>$coin)
            {
//                if($coin_id!=0 && $user->invite_num<db_config('INVITE_MINE_LIMIT')){
//                    continue;//邀请人不超过xx人时，不允许挖主流币
//                }
                //==== 1. 先计算应得奖金总额
                $mine_pool = MinePool::where(["coin_type"=>$coin_id])->first();
                $fvc_base = $mine_pool->total_amount; //剩余的可以用于计算挖矿利润的基数
                $fvc_base = $fvc_base * $coin['mine_percent']; //拿出用于计算的部份进行计算
                $fvc_base = $fvc_base * $cp / $cp_total;  //根据算力算出个人可得的奖金基数
                if($fvc_base<=0)$fvc_base=0;

                $rebate_period_second = intval($coin['mine_peroid']) * 24 * 3600; //返还的周期(秒数，用于计算每秒的基本收益)
                $fvc_second = $fvc_base/$rebate_period_second; //每秒的收益
                $pass_time = $now - $mined_time;  //计算已挖取的时间
                $max_time = 3600 * $max_hours;
                $pass_time = $pass_time>$max_time?$max_time:$pass_time; //不能超过最长时间
                $fvc = $fvc_second * $pass_time; //计算出应得的收益
                if($fvc<=0)$fvc=0;
                //至此，矿已挖出, $fvc即新生成的矿，需要分配到格子中

                //第二步，只有当有奖金产生时才会进行分配
                if($fvc>0)
                {
                    //==== 2. 将可领取的奖金按一定的随机比例，分配到容器中去
                    $divs = $coin['mine_div']; //可以分成的分数
                    $unmined_list = MineLog::where(["user_id"=>$user->id,"mined"=>0,"coin_type"=>$coin_id])->orderBy("amount","asc")->get(); //未领取的记录，从小到大排序，先放到少的格子里
                    //计算理论上本次每格填满的值，当次计算中，如果某格填满，则改为fixed，表示不会再分配进去
                    $max_item_fvc = $fvc_second*($max_time/$divs);

                    $min_item_fvc = $max_item_fvc *(($this->min_minute*60)/($max_time/$divs));  //每格最少要放的金额，运行到底要分几份，后续可按阶梯，表示总份数越大，每份填的越大，产出第二份divs为2的周期就越长
                    do{
                        $fvc_item = $fvc/$divs; //基数
                        if($fvc_item>=$min_item_fvc)
                        {
                            break;
                        }
                        if($divs>1){
                            $divs--;
                        }
                        else
                        {
                            break;
                        }
                    }while(true);


                    //开始将fvc分配进奖格
                    //先将奖金存入未满的奖格，只有一个未满的前提下， 即使过了48小时，即使奖池突然增加，导致fvc-unmined_total的值特别大，也只会填满当前的奖格
                    //分配方案为，将奖金10等分，计算出每份的奖金，再正负10%随机，放入未满的格子中，格子不够创建一个，如果满了则设置为fixed，如果每份数量少于格子的单次进格子的数量，则全部进当前格子
                    $miner_amount = 0; //矿机产生的奖励
                    for($i=0;$i<$divs;$i++)
                    {
                        $mine_log = $unmined_list[$i];
                        if($mine_log->fixed==1)continue;//当前格已满，跳过，这份奖金将获取不到
                        if(!$mine_log)
                        {
                            $mine_log = new MineLog();
                            $mine_log->user_id = $user->id;
                            $mine_log->create_time = $now;
                            $mine_log->coin_type = $coin_id;
                            $mine_log->max_amount = $max_item_fvc;  //生成时就固定下本格最多可存放的数量
                        }
                        $fvc_current = $fvc_item*rand(80,100)/100;//负20%浮动
                        $fvc_current = $fvc_current>$fvc?$fvc:$fvc_current; //不可超出剩余的数量
                        $add_fvc = 0;  //本次增加的收益
                        if($fvc_current>$mine_log->amount)
                        {
                            $add_fvc = $fvc_current - $mine_log->amount;
                            $mine_log->amount = $fvc_current*($user->cp_total/$cp);
                        }

                        if($mine_log->amount>=$mine_log->max_amount){
                            $mine_log->fixed = 1;
                            $mine_log->fixed_time = $now;
                        }
                        $mine_log->update_time = time();
                        $mine_log->save();

                        $miner_amount += $fvc_current;

                        //扣掉矿池中的币
                        $mine_pool->total_amount -= $add_fvc;
                    }
                    $mine_pool->save();
                }
            } //end coin_foreach

            DB::commit();
        }catch(\Exception $e)
        {
            DB::rollback();
        }
    }
    /**
     * @name compute
     * @description 计算可领取的挖矿收益
     * @param $param 无
     * @return array
     * mine_coin: [
            {
                id: 146,
                amount: "1.32876712",
                coin_unit: "BCTY",
                icon :图标地址
            },
            {
                id: 147,
                amount: "1.31506849"
                coin_unit: "BCTY"
                 icon :图标地址
    },
            {
                id: 160,
                amount: "0.00342466"
                coin_unit: "BCTY"
                icon :图标地址
    },......
        ]， 最多10份，UI自行展示
     */
    public function compute($param)
    {
        //算法逻辑修改
        //1. 先计算出应该发给玩家的可领取奖金，即挖出的矿，总量的固定的，按照单位时间来计算(其他算法时，主要修改该项奖金的算法，如（修改为全网算力）)，挖出的矿不再参与下次的计算，即在此处就要扣除user表的fvc_mined
        //2. 将可领取的奖金按一定的随机比例，分配到容器中去
        $user = $this->user;
        $this->compute_mine($user);
        $this->miner_mine($user);
        $coin_types = CoinType::where("status",1)->get();
        $coin_type_configs = [];
        $coin_type_configs[0] = ["icon"=>db_config('COIN_ICON'),"coin_unit"=>db_config("COIN_UNIT"),"mine_percent"=>db_config("MINE_PERCENT"),"mine_peroid"=>db_config("MINE_PEROID"),"mine_div"=>db_config("MINE_DIV"),"mine_min"=>db_config("MINE_MIN")];
        foreach($coin_types as $row)
        {
            $data = ["icon"=>$row->icon,"coin_unit"=>$row->coin_unit,"mine_percent"=>$row->mine_percent,"mine_peroid"=>$row->mine_peroid,"mine_div"=>$row->mine_div,"mine_min"=>$row->mine_min];
            $coin_type_configs[$row->id] = $data;
        }
        $items = MineLog::where(["user_id"=>$user->id,"mined"=>0])->select("id","amount","fixed","coin_type","max_amount")->orderBy("id","asc")->get(); //未领取的格子
        $miner_items = MinerMineLog::where(["user_id"=>$user->id,"mined"=>0])->orderBy("id","asc")->get();//未领取的矿机格子
        $mine_coin = [];
        foreach ($items as $k=>$val)
        {
            $max_amount = $items[$k]->max_amount;
            $miner_items[$k]->mine_type = 0;
            $mine_div = $coin_type_configs[$val->coin_type]['mine_div'];
            $max_hours = $this->max_hours;
            //最小的掘金数量为当前最大份额的10分钟产量
            $min_amount = $max_amount * (($this->min_minute*60)/($max_hours*3600/$mine_div));
            if($val->coin_type==0) {
                $items[$k]->coin_unit = db_config("COIN_UNIT");
                $items[$k]->icon = db_config("COIN_ICON");
                $min_item_fvc = $coin_type_configs[$val->coin_type]['mine_min'];
            }
            else{
                $items[$k]->icon = img($coin_type_configs[$val->coin_type]['icon']);
                $items[$k]->coin_unit = coin_config("COIN_UNIT",$val->coin_type);
                $min_item_fvc = $coin_type_configs[$val->coin_type]['mine_min'];
            }
            $items[$k]->amount = Helper::trimNumber($val->amount,config("app.mined_decimals"));
            $min_amount = $min_amount<$min_item_fvc?$min_amount:$min_item_fvc;
            if($val->amount<$min_amount&&$val->fixed==0)
            {
                unset($items[$k]);
            }
            else
            {
                if(count($mine_coin)<10)
                $mine_coin[] = $items[$k];
                else
                    break;
            }

        }
        foreach($miner_items as $k=>$val){
            $miner_items[$k]->max_amount = $miner_items[$k]->amount;
            $miner_items[$k]->mine_type = 1;
            if($val->coin_type==0) {
                $miner_items[$k]->coin_unit = db_config("COIN_UNIT");
                $miner_items[$k]->icon = db_config("COIN_ICON");
            }
            else{
                $miner_items[$k]->icon = img($coin_type_configs[$val->coin_type]['icon']);
                $miner_items[$k]->coin_unit = coin_config("COIN_UNIT",$val->coin_type);
            }
            $miner_items[$k]->amount = Helper::trimNumber($val->amount,config("app.mined_decimals"));
            if(count($mine_coin)<10)
                $mine_coin[] = $miner_items[$k];
            else
                break;
        }
        $this->setData("mine_coin",$mine_coin); //空的可领取份额
        return $this->success();

    }

    /**
     * @name compute_steal
     * @description 偷币的计算
     * @param $param
     * user_id:偷取的好友ID
     * @return array
     * 返回的结构与compute接口相同
     */
    public  function compute_steal($param){
        $user_id = $param['user_id'];

        $user = User::leftJoin("user_follow",function($join){
            $join->on('user_follow.follow_user_id','=',"user.id")->where("user_follow.user_id",'=',$this->user->id);
        })->leftJoin("user_follow as user_fans",function($join){
            $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
        })->whereRaw('fanwe_user_fans.id is not null')->whereRaw('fanwe_user_follow.id is not null')
            ->where("user.id",$user_id)
            ->select("user.*")->first();

        if(!$user)
            return $this->error("只能偷取好友的币");

        $this->compute_mine($user);

        $friend_cp = $user->cp_total;
        $my_cp = $this->user->cp_total;
        $total_cp = $friend_cp+$my_cp;

        $coin_types = CoinType::where("status",1)->get();
        $coin_type_configs = [];
        $coin_type_configs[0] = ["coin_unit"=>db_config("COIN_UNIT"),"mine_percent"=>db_config("MINE_PERCENT"),"mine_peroid"=>db_config("MINE_PEROID"),"mine_div"=>db_config("MINE_DIV"),"mine_min"=>db_config("MINE_MIN")];
        foreach($coin_types as $row)
        {
            $data = ["coin_unit"=>$row->coin_unit,"mine_percent"=>$row->mine_percent,"mine_peroid"=>$row->mine_peroid,"mine_div"=>$row->mine_div,"mine_min"=>$row->mine_min];
            $coin_type_configs[$row->id] = $data;
        }

        $steal_logs = StealLog::where(["user_id"=>$this->user->id,"from_user_id"=>$user->id,"create_date"=>date("Y-m-d")])->get();  //当天偷取的记录
        foreach($steal_logs as $log)
        {
            $coin_type_configs[$log->coin_type]['fetch_div'] = intval($coin_type_configs[$log->coin_type]['fetch_div']);
            $coin_type_configs[$log->coin_type]['fetch_div']++;  //记录每种币的领取次数，当超过mine_div时，列表中就要unset掉
        }

        $items = MineLog::where(["user_id"=>$user->id,"mined"=>0])->select("id","amount","fixed","coin_type","max_amount")->orderBy("id","asc")->get(); //未领取的格子
        $mine_coin = [];
        foreach ($items as $k=>$val)
        {
            //最小的偷币数量为当前最大可偷份额的10分钟产量
            $max_amount = $val->max_amount * $my_cp / $total_cp;
            $mine_div = $coin_type_configs[$val->coin_type]['mine_div'];
            $max_hours = $this->max_hours;
            $min_amount = $max_amount * (($this->min_minute*60)/($max_hours*3600/$mine_div));

            $amount = $val->amount * $my_cp / $total_cp;
            $items[$k]->amount = Helper::trimNumber($amount,5);
            if($val->coin_type==0) {
                $items[$k]->coin_unit = db_config("COIN_UNIT");
                $min_item_fvc = $coin_type_configs[$val->coin_type]['mine_min'];
            }
            else{
                $items[$k]->coin_unit = coin_config("COIN_UNIT",$val->coin_type);
                $min_item_fvc = $coin_type_configs[$val->coin_type]['mine_min'];
            }

            $min_amount = $min_amount>$min_item_fvc?$min_amount:$min_item_fvc;
            $unset = false;
            if($val->amount<$min_amount&&$val->fixed==0)
            {
                unset($items[$k]);
                $unset = true;
            }
            if($val->amount<=0)
            {
                unset($items[$k]);
                $unset = true;
            }

            $coin_type_configs[$val->coin_type]['fetch_div'] = intval($coin_type_configs[$val->coin_type]['fetch_div']);
            //判断是否已经超出
            if($coin_type_configs[$val->coin_type]['fetch_div']>=$coin_type_configs[$val->coin_type]['mine_div'])
            {
                unset($items[$k]);
                $unset = true;
            }
            else
            {
                $coin_type_configs[$val->coin_type]['fetch_div']++;
            }

            if(!$unset)
            {
                if(count($mine_coin)<10)
                $mine_coin[] = $items[$k];
                else
                    break;
            }
        }
        $this->setData("mine_coin",$mine_coin); //空的可领取份额
        return $this->success();

    }


    /**
     * @name fetch
     * @description 领取挖矿挖出的收益
     * @param $param id:由compute接口下发的每个份额的ID
     * @return
     */
    public function fetch($param){
        $type = intval($param['type']);
        if($type == 0){
            $id = intval($param['id']);
            $mine_log = MineLog::where(["id"=>$id,"user_id"=>$this->user->id,"mined"=>0])->first();
            if(!$mine_log)
            {
                return $this->error("无效的挖宝记录");
            }
            DB::beginTransaction();
            try{
                $now = time();
                $mine_log->fixed_time = $now;
                $mine_log->fixed = 1;
                $mine_log->mined_time = $now;
                $mine_log->mined = 1;
                $mine_log->save();
                Helper::incomeCoin("mine",$mine_log->id);
                $this->user->save();
                DB::commit();
                return $this->success();
            }catch (\Exception $e)
            {
                DB::rollback();
                return $this->error($e->getMessage());
            }
        }else{
            $id = intval($param['id']);
            $MinerMineLog = MinerMineLog::where(["id"=>$id,"user_id"=>$this->user->id,"mined"=>0])->first();
            if(!$MinerMineLog)
            {
                return $this->error("无效的挖宝记录");
            }
            DB::beginTransaction();
            try{
                $now = time();
                $MinerMineLog->mined_time = $now;
                $MinerMineLog->mined = 1;
                Helper::incomeCoin("miner_mine",$MinerMineLog->id);
                $MinerMineLog->save();
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


    /**
     * @name fetch_steal
     * @description 领取偷到的收益
     * @param $param id:由compute_steal接口下发的每个份额的ID
     * @return
     */
    public function fetch_steal($param){
        DB::beginTransaction();
        try{
            $id = intval($param['id']);
            $mine_log = MineLog::where(["id"=>$id,"mined"=>0])->first();
            if(!$mine_log)
            {
                return $this->error("无效的记录");
            }
            //判断是否为好友
            $user_id = $mine_log->user_id;
            $user = User::leftJoin("user_follow",function($join){
                $join->on('user_follow.follow_user_id','=',"user.id")->where("user_follow.user_id",'=',$this->user->id);
            })->leftJoin("user_follow as user_fans",function($join){
                $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
            })->whereRaw('fanwe_user_fans.id is not null')->whereRaw('fanwe_user_follow.id is not null')
                ->where("user.id",$user_id)
                ->select("user.*")->first();
            if(!$user)
                return $this->error("只能偷取好友的币");

            //计算可偷取的份额
            $friend_cp = $user->cp_total;
            $my_cp = $this->user->cp_total;
            $total_cp = $friend_cp+$my_cp;
            $amount = $mine_log->amount * $my_cp / $total_cp;

            if($mine_log->amount<$amount)$amount = $mine_log->amount;
            $mine_log->amount -= $amount;
            $mine_log->save();

            $now = time();
            $steal_log = new StealLog();
            $steal_log->coin_type = $mine_log->coin_type;
            $steal_log->user_id = $this->user->id;
            $steal_log->from_user_id = $user->id;
            $steal_log->amount = $amount;
            $steal_log->create_time = date("Y-m-d H:i:s",$now);
            $steal_log->create_date = date("Y-m-d",$now);
            $steal_log->save();

            Helper::incomeCoin("steal",$steal_log->id);
            $this->user->save();
            DB::commit();
            return $this->success();
        }catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name rank
     * @description 算力 排行榜
     * @param
     * num：条数，不传参，默认获取10条
     * @return rank
     * [
     * "username" => 用户名,
     * "cp_total" => 算力
     * ]
     */
    public function rank($param)
    {
        $num = $param["num"]?$param["num"]:10;
        $rank = User::select("username",DB::raw("round(cp_total) as cp_total"))->orderBy("cp_total","desc")->orderBy("id","asc")->limit($num)->get();
        $this->setData("rank",$rank);
        return $this->success();
    }

}