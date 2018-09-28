<?php
namespace App\Open\Services\App\Open;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CalcPush;
use App\Http\Models\CoinType;
use App\Http\Models\DuobaoOrder;
use App\Http\Models\Exchange;
use App\Http\Models\LotteryOrder;
use App\Http\Models\PlatformOpenid;
use App\Http\Models\PromoterReward;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DuobaoApi extends FanweBaseService
{

    /**
     * @name exchange
     * @description 兑换币
     * @param
     * openid :
     * amount：数量
     * security : security
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
     public function exchange($param)
    {
        $amount = $param["amount"];
        $security= $param['security'];
        $coin_type = 0;
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        if(!$amount)
        {
            return $this->error("请输入兑换数量",FanweErrcode::INCHARGE_AMOUNT_NOT_EXSITS);
        }
        if(!$security)
        {
            return $this->error("请输入资金密码",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if(md5($security)!=$user->security)
        {
            return $this->error("资金密码错误",FanweErrcode::USER_SECURITY_ERROR);
        }
        $userAsset = UserAsset::where('user_id',$user->id)->where('coin_type',$coin_type)->first();
        if($amount>$userAsset->vc_total)
        {
            return $this->error("兑换数量不足",FanweErrcode::USER_EXCHANGE_AMOUNT_OVER);
        }
        DB::beginTransaction();
        try{
            $exchange = new Exchange();
            $exchange->user_id = $user->id;
            $exchange->create_time = date("Y-m-d H:i:s",time());
            $exchange->api = "duobao";
            $exchange->memo = "夺宝平台兑换币 ".$amount;
            $exchange->vc_amount = $amount;
            $exchange->coin_type = $coin_type;

            //先扣除不可交易的部分
            $diff_amount = $amount - $user->vc_untrade;
            if($diff_amount>0)
            {
                $exchange->vc_normal = $diff_amount;
                $exchange->vc_untrade = $user->vc_untrade;
            }
            else
            {
                $exchange->vc_untrade = $amount;
            }

            $exchange->save();
            //生成支出
            Helper::expendCoin("exchange",$exchange->id);
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }


    /**
     * @name order
     * @description 中奖结果推送 用于累计矿池等
     * @param
     * array(
     * order_sn  流水号(随机生成)
     * openid
     * coin_type_data =>array(
     * 0=>array('coin_type'=>,'num' =>,)
     * )
     *
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function order($param){
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        $param['lottery_data'] = unserialize($param['lottery_data']);
        if(!$param['order_sn']){
            return $this->error('缺少流水号',FanweErrcode::SN_MISS);
        }
        $duobao_order = DuobaoOrder::where('order_sn',trim($param['order_sn']))->count();
        if($duobao_order>0){
            return $this->error('流水号已存在',FanweErrcode::SN_EXIST);
        }


        $coin_type = CoinType::all()->pluck('id');
        if($coin_type){
            $coin_type =  array_merge($coin_type->toArray(),[0]);
        }else{
            $coin_type = [0];
        }
        if(!empty($param['lottery_data'])){
            try{
                DB::beginTransaction();
                foreach ($param['lottery_data'] as $key=>$val){
                    if(in_array($val['coin_type'],$coin_type)==false){
                        throw new \Exception('coin_type not exist');
                    }
                    $coin_info = CoinType::where('id',$val['coin_type'])->first();
                    $duobao_id = DB::table('duobao_log')->insertGetId([
                        'to'=>$user->id,
                        'vc_amount'=>$val['num'],
                        'create_time'=>date("Y-m-d H:i:s"),
                        'coin_type'=>$val['coin_type'],
                        'memo'=>'一元夺宝中奖'
                    ]);
                    Helper::incomeCoin('duobao',$duobao_id);
                    $order = new DuobaoOrder();
                    $order->order_sn = trim($param['order_sn']);
                    $order->amount = $val['num'];
                    $order->create_time = date('Y-m-d H:i:s');
                    $order->coin_type = intval($val['coin_type']);
                    $order->user_id = $user->id;
                    $order->log = json_encode($param);
                    $order->save();
                    if($val['coin_type']>0){
                        Helper::changePool($order->amount * $coin_info->pool_rate,1,$order->coin_type,'夺宝进入矿池','duobao',$user->id);
                    }elseif ($val['coin_type']==0){
                        Helper::changePool($order->amount * db_config('COIN_MINE_RATE'),1,$order->coin_type,'夺宝进入矿池','duobao',$user->id);
                    }
                }
                DB::commit();
                return $this->success();
            }   catch (\Exception $e)
            {
                Log::warn($e->getMessage());
                DB::rollback();
                return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
            }
        }else{
            return $this->error();
        }
    }

    /**
     * @name cointype
     * @description  返回夺宝币种支持类型
     * @param
     * @return
     * 成功：coin_type =>[
     * [
     * 'coin_type'=>类型id,
     * 'icon' => 图标,
     * 'coin_unit' => 单位,
     * 'price' => 人民币单价
     * ]...]
     * 失败：返回错误码及提示
     */
    public function cointype(){
        $coin_type = CoinType::select('id as coin_type','icon','coin_unit','price','name')->get();
        if($coin_type){
          $data = $coin_type->toArray();
        }else{
            $data =[];
        }
        $default = ['coin_type'=>0,'icon'=>db_config('COIN_ICON'),'coin_unit'=>db_config('COIN_UNIT'),'price'=>db_config('COIN_PRICE'),'name'=>db_config('COIN_CNNAME')];
        $data[]=$default;
        $this->setData('coin_type',$data);
        return $this->success();
    }


    /**
     * @name calcreturn
     * @description  消费后算力返还
     * @param
     * 'openid'
     * 'sn'=>'流水号'
     * 'amount'=>金额,
     * 'type' => '' 夺宝 'duobao' '竞彩' lottery 其他待定
     * ]
     * @return  成功：null
     * 失败：返回错误码及提示
     */
    public function calcreturn($param){

        if(!$param['sn']){
            return $this->error('无流水号',FanweErrcode::SN_MISS);
        }
        $count = CalcPush::where('sn',$param['sn'])->count();
        if($count>0){
            return $this->error('流水号重复',FanweErrcode::USER_NOT_EXSITS);
        }
        $user = PlatformOpenid::user($param);
        if(!$user){
            return $this->error('用户不存在',FanweErrcode::USER_NOT_EXSITS);
        }
        if(!is_numeric($param['amount']) || $param['amount']<=0){
            return $this->error('金额错误',FanweErrcode::USER_PAY_AMOUNT_NOT_EXSITS);
        }
        try{
            $calc_push = new CalcPush();
            $calc_push->sn = $param['sn'];
            $calc_push->amount = $param['amount'];
            $calc_push->log = json_encode($param);
            $calc_push->user_id = $user->id;
            $calc_push->create_time = date('Y-m-d H:i:s');
            $calc_push->save();
            DB::beginTransaction();
            Helper::GrantCp($user->id,0,$param['amount']*db_config('DUOBAO_CALC_RATE'),$param['type']);
            DB::commit();
        }   catch (\Exception $e)
        {
            Log::warn($e->getMessage());
            DB::rollback();
            return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
        }
        if(!$user->pid) {
            return $this->success();
        }
        $parent = User::find($user->pid);
        $i = 0;
        while($i<10 && $parent){
            if($parent->flow_status ==2){//最近的流量主
                $reword_log = new PromoterReward();
                $reword_log->total_amount = $param['amount'];
                $reword_log->from_user_id = $user->id;
                $reword_log->user_id = $parent->id;
                $reword_log->create_time = date('Y-m-d H:i:s');
                $reword_log->create_date = date('Y-m-d');
                $reword_log->create_date_month = date('Y-m');
                if($param['type']=='duobao'){
                    $reword_log->memo = "一元夺宝消费{$param['amount']}";
                }else if ($param['type']=='lottery'){
                    $reword_log->memo = "竞彩消费{$param['amount']}";
                }
                $reword_log->api = $param['type'];
                $reword_log->save();
                $parent->achievement += $param['amount'];
                $parent->save();
                break;
            }else if($parent->pid){
                $parent = User::find($parent->pid);
            }else{
                break;
            }
            $i++;
        };
        return $this->success();
    }

    /**
     * @name lottery_result
     * @description  竞猜结果推送
     * @param
     * 'order_sn'=>'流水号'
     * 'lottery_data'=>[[
     * 'coin_type'=>'1'
     * 'openid'=> 'xxxx',
     * 'amount'=>5],
     * [
     * 'coin_type'=>'2'
     * 'openid'=> 'xxxx',
     * 'amount'=>3]
     * ]
     * ]
     * @return  成功：null
     * 失败：返回错误码及提示
     */
    public function lottery_result($param){

        if(!$param['order_sn']){
            return $this->error('缺少流水号',FanweErrcode::SN_MISS);
        }
        $lottery_order = LotteryOrder::where('order_sn',trim($param['order_sn']))->count();
        if($lottery_order>0){
            return $this->error('流水号已存在',FanweErrcode::SN_EXIST);
        }
        $coin_type = CoinType::all()->pluck('id')->toArray();
        $coin_type[]=0;
        if(!empty($param['lottery_data'])){
            try{
                $param['lottery_data']= json_decode($param['lottery_data'],true);
                DB::beginTransaction();
                foreach ($param['lottery_data'] as $key=>$val){
                    if(in_array($val['coin_type'],$coin_type)==false){
                        throw new \Exception('coin_type not exist');
                    }
                    $user = PlatformOpenid::user(['openid'=>$val['openid']]);
                    if(!$user){
                        Log::warn('user not exsit openid '.$val['openid']);
                        continue;
                    }
                   // $coin_info = CoinType::where('id',$val['coin_type'])->first();
                    $lottery_log = DB::table('lottery_log')->insertGetId([
                        'to'=>$user->id,
                        'vc_amount'=>$val['amount'],
                        'create_time'=>date("Y-m-d H:i:s"),
                        'coin_type'=>$val['coin_type'],
                        'memo'=>'竞彩'
                    ]);
                    Helper::incomeCoin('lottery',$lottery_log);
                    $order = new LotteryOrder();
                    $order->order_sn = trim($param['order_sn']);
                    $order->amount = $val['amount'];
                    $order->create_time = date('Y-m-d H:i:s');
                    $order->coin_type = intval($val['coin_type']);
                    $order->user_id = $user->id;
                    $order->log = json_encode($param);
                    $order->save();
                }
                DB::commit();
                return $this->success();
            }   catch (\Exception $e)
            {
                Log::warn($e->getMessage());
                DB::rollback();
                return $this->error('操作失败',FanweErrcode::SYSTEM_ERROR);
            }
        }else{
            return $this->error('empty lottery_data',FanweErrcode::SYSTEM_ERROR);
        }
    }

}