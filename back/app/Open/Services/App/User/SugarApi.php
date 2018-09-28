<?php
namespace App\Open\Services\App\User;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\Sugar;
use App\Http\Models\SugarAuth;
use App\Http\Models\SugarLog;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class SugarApi extends FanweAuthService
{

    /**
     * @name index
     * @description 糖果首页
     * @param
     * page
     * type 0 所有糖果 1我领取的 2我发放的
     * sugar_type  0糖果 1转账
     * @return
     * 成功：data:{sugars:[
     * {
            id: 糖果id 跳转领取详情需用到,
            amount: 糖果发放总量,
            less_amount: 糖果发放剩余数量,
            lock_day: 锁仓天数,
            receive_end_time: 领取截止时间,
            receive_end_day : 领取截止天数
            copys: 总份数,
            copys_less: 剩余份数,
            copys_amount: 每份数量,
            coin_info: {
            coin_type: 0,
            coin_unit: 单位
            },
            have: 是否已领过
            }
     *      desc:"可用描述文字  如 已可用、剩余2天可用"
     * ]}
     * 失败：返回错误码及提示
     */
    public function index($param)
    {
        $user = $this->user;
        $coin_type_all = CoinType::getAll();
        if($param['type']==0){
            $lists = Sugar::where(function ($query){
                $query->where('copys_less','>','0');
                $query->where('less_amount','>=','copys_amount');
                $query->where('receive_end_time','>',date('Y-m-d H:i:s'));
            })->orderBy('id','desc')->paginate(10);
            $lists = $lists->map(function ($val)use ($user,$coin_type_all){
                $sugar_log = SugarLog::where('to',$user->id)->where('sugar_id',$val->id)->first();
                $val->coin_info = $coin_type_all[$val['coin_type']];
                $val->receive_end_day = ceil( (strtotime($val->receive_end_time) - time())/86400);
                $val->amount = Helper::formatCoin($val->amount,1);
                if($sugar_log){
                    $val->have = 1;
                    $diff = strtotime($sugar_log->lock_end_time)-time();
                    if(!$sugar_log->free){
                        if($diff>0){
                            $day = ceil($diff/86400);
                            $val->desc = "{$day}天后可用";
                        }else{
                            $val->desc = '即将可用';
                        }
                    }else{
                        $val->desc = "已可用";
                    }
                }else{
                    $val->have = 0;
                }
                return $val;
            })->toArray();
        }else if($param['type']==1){
            $lists = SugarLog::with('sugar','from_user')->where(function ($query)use ($user,$param){
                $query->where('to','=',$user->id);
            })->orderBy('sugar_time','desc')->paginate(10);
            $lists = $lists->map(function ($val)use($coin_type_all,$param){
                $val->coin_info = $coin_type_all[$val['coin_type']];
                $val->amount = Helper::formatCoin($val->amount,1);
                if(!$val->free){
                    $diff = strtotime($val->lock_end_time)-time();
                    if($diff>0){
                        $day = ceil($diff/86400);
                        $val->desc = "{$day}天后可用";
                    }else{
                        $val->desc = '即将可用';
                    }
                }else{
                    $val->desc = "已可用";
                }
                $val->copys =$val->sugar->copys;
                $val->copys_less =$val->sugar->copys_less;
                $val->lock_day =$val->sugar->lock_day;
                $val->copys_amount =$val->sugar->copys_amount;
                $val->receive_end_time = $val->sugar->receive_end_time;
                $val->receive_end_day = ceil( (strtotime($val->receive_end_time) - time())/86400);
                unset($val->sugar);
                return $val;
            })->toArray();
        }else {
            $lists = Sugar::with('to_user')->where(function ($query)use ($user,$param){
                $query->where('user_id',$user->id);
            })->orderBy('id','desc')->paginate(10);
            $total = $lists->total();
            $this->setData('total',$total);
            $lists = $lists->map(function ($val)use ($coin_type_all){
                $val->free_amount = SugarLog::where('id',$val->sugar_id)->where('free','1')->sum('sugar_amount');
                $val->coin_info = $coin_type_all[$val['coin_type']];
                $val->amount = Helper::formatCoin($val->amount,1);
                $val->free_amount = Helper::formatCoin($val->free_amount,1);
                return $val;
            })->toArray();
        }

        $this->setData('sugars',$lists);
        return $this->success();
    }

    /**
     * @name grant
     * @description 发糖
     * @param
     * coin_type 币种
     * amount 数量
     * copys 份数
     * copys_amount 每份数量
     * lock_day 锁仓天数
     * receive_limit 领取天数限制
     * sugar_fee 手续费
     * @return
     * 成功：data:{
     * }
     * 失败：返回错误码及提示
     */
    public function grant($param)
    {
        if(!$param['amount']){
            return $this->error('请输入发糖数量');
        }
        if(!$param['copys']){
            return $this->error('请输入发糖份数');
        }
        if(!$param['copys_amount']){
            return $this->error('请输入每份数量');
        }
        $param['lock_day'] = intval($param['lock_day']);
        if(!is_numeric($param['lock_day']) || $param['lock_day']<0){
            return $this->error('请输入天数');
        }
        if(!is_numeric($param['receive_limit'])){
            return $this->error('请输入领取天数限制');
        }
        $user = $this->user;
        $sugar_auth = SugarAuth::where('user_id',$user->id)->where('coin_type',$param['coin_type'])->first();
        if(!$sugar_auth || $sugar_auth->status!=1||$user->vc_total < $sugar_auth->min_limit){
            return $this->error('没有权限',FanweErrcode::AUTH_ERROR);
        }
        if(!$param['security'])
        {
            return $this->error('请输入资金密码');
        }
        if(md5($param['security'])!=$user->security)
        {
            return $this->error('资金密码错误');
        }

        if($param['coin_type']==0){
            $rate = 1;
        }else{
            $plaprice = db_config('COIN_PRICE');
            $coin_type_price = CoinType::find($param['coin_type']);
            $rate =  $coin_type_price->price /$plaprice;
        }
        $sugar_fee = round($sugar_auth->receive_fee * $param['amount']*$rate,5);
        if($param['coin_type']==0){
            $need_total = $sugar_fee + $param['amount'];
            if($need_total>$user->vc_total){
                return $this->error("金额不足,含手续费需要{$need_total} ".db_config('COIN_UNIT'));
            }
        }else{
            $user_asset = UserAsset::where('user_id',$user->id)->where('coin_type',$param['coin_type'])->first();
            if($user_asset->vc_total < $param['amount']){
                return $this->error('金额不足');
            }
            if($user->vc_total < $sugar_fee){
                return $this->error("手续费不足");
            }
        }
        try {
            DB::beginTransaction();
            $sugar = new Sugar();
            $sugar->create_time = date('Y-m-d H:i:s');
            $sugar->user_id = $user->id;
            $sugar->coin_type = $param['coin_type'];
            $sugar->amount = $param['amount'];
            $sugar->less_amount = $param['amount'];
            $sugar->lock_day = $param['lock_day'];
            $sugar->receive_limit = $param['receive_limit'];
            $sugar->receive_end_time = date('Y-m-d H:i:s',strtotime("+{$param['receive_limit']} day"));
            $sugar->copys = $param['copys'];
            $sugar->copys_less = $param['copys'];
            $sugar->copys_amount = $param['copys_amount'];
            $sugar->sugar_fee = $sugar_fee;
            $sugar->save();
            Helper::expendCoin('sugar',$sugar->id);//发糖支出
            Helper::expendCoin('sugar_fee',$sugar->id);//发糖手续费支出
            $sugar_auth->sugar_time++;
            $sugar_auth->sugar_total +=$sugar->amount;
            $sugar_auth->save();
            DB::commit();
            $this->setData('sugar',$sugar);
            return $this->success();
        }catch (\Exception $e)
        {
            DB::rollback();
            Log::warn($e->getMessage());
            return $this->error();
        }
    }

    /**
     * @name receive
     * @description 领糖
     * @param
     * sugar_id 糖id
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function receive($param)
    {
        $user = $this->user;
        $sugar = Sugar::find($param['sugar_id']);
        if(!$sugar){
            return $this->error('网络错误');
        }
        if($sugar->less_amount <= 0){
            return $this->error('剩余数量不足');
        }
        if($sugar->copys_less <=0){
            return $this->error('已经发放完毕');
        }
        if(strtotime($sugar->receive_end_time) < time()){
            return $this->error('领取时间已过');
        }
        $sugar_log = SugarLog::where('to',$user->id)->where('sugar_id',$param['sugar_id'])->first();
        if($sugar_log){
            return $this->error('已经领取过');
        }

        try {
            DB::beginTransaction();
            $sugar_log = new SugarLog();
            $sugar_log->from = $sugar->user_id;
            $sugar_log->to = $user->id;
            $sugar_log->create_time = date('Y-m-d H:i:s');
            $sugar_log->sugar_amount = $sugar->copys_amount;
            if($sugar->less_amount < $sugar->copys_amount){
                $sugar_log->sugar_amount = $sugar->less_amount;
            }
            $sugar_log->lock_end_time = date('Y-m-d H:i:s',strtotime("+ {$sugar->lock_day} day"));
            $sugar_log->coin_type = $sugar->coin_type;
            $sugar_log->sugar_time = $sugar->create_time;
            $sugar_log->sugar_id = $sugar->id;
            $sugar_log->free = 0;
            if($sugar->lock_day==0){//不锁仓直接可用
                $sugar_log->free = 1;
            }
            $sugar_log->save();
            $sugar->copys_less--;
            if($sugar->copys_less<0){
                throw new \Exception("copys_less no enough:".$sugar->id);
            }
            $sugar->less_amount -= $sugar_log->sugar_amount;
            if($sugar->less_amount<0){
                throw new \Exception("less_amount no enough:".$sugar->id);
            }
            $sugar->save();
            Helper::incomeCoin('sugar',$sugar_log->id);//增加领糖收入
            if($sugar->lock_day!=0){
                $sugar_log->freeze_log_id = Helper::freezeCoin('sugar',$sugar_log->id);//锁仓冻结
                $sugar_log->save();
            }
            DB::commit();
            if($sugar_log->free){
                $desc = '已可用';
            }else{
                $day = ceil((strtotime($sugar_log->lock_end_time) - time())/86400);
                $desc = "{$day}天后可用";
            }
            $this->setData('desc',$desc);
            return $this->success('领取成功');
        }catch (\Exception $e)
        {
            DB::rollback();
            Log::warn($e->getMessage());
            return $this->error();
        }
    }

    /**
     * @name grant_detail
     * @description 领取详情
     * @param
     * sugar_id
     * @return
     * 成功：data:{sugar:{}}
     * 失败：返回错误码及提示
     */
    public function grant_detail($param)
    {
        $lists = SugarLog::with('to_user')->where('sugar_id',$param['sugar_id'])->paginate(10);
        $lists = $lists->map(function ($val){
            $val->to_username = $val->to_user->username;
            $val->sugar_amount = Helper::formatCoin($val->sugar_amount,1);
            unset($val->to_user);
            return $val;
        })->toArray();
        $this->setData('detail',$lists);
        return $this->success();
    }

    /**
     * @name auth_info
     * @description 币种金额信息及权限信息
     * @param
     * sugar_id
     * @return
     * 成功：data:{
                auth_info: {
                0: {
                coin_type: 币种,
                icon: 图标
                coin_unit: 单位名称,
                price: 价格,
                name: 名称,
                assets: {
                coin_type: 0,
                amount: 此币种资产
                },
                auth_info: {
                status: 1, 是否有权限
                receive_fee: 手续费比率,
                min_limit: 资产低于此时视为无权限
                }
                },
     * }
     * 失败：返回错误码及提示
     */
    public function sugar_auth($parma)
    {
        $user = $this->user;
        $lists = CoinType::getAll();
        $assets = UserAsset::where('user_id',$user->id)->get()->keyBy('coin_type')->toArray();
        $auth_info = SugarAuth::where('user_id',$user->id)->get()->KeyBy('coin_type')->toArray();
        $lists = collect($lists)->map(function ($val,$key)use($assets,$auth_info,$user){
            if($assets[$key]){
                $val['assets'] = $assets[$key];
                $val['assets']['amount'] =  Helper::formatCoin($val['assets']['vc_total'],1);
            }else{
                $val['assets']['amount'] =  Helper::formatCoin(0.00000,1);
            }
            if($auth_info[$key]){
                $val['auth_info'] = $auth_info[$key];
            }else{
                $val['auth_info']['status'] = 0;
            }
            return $val;
        })->toArray();
        $this->setData('auth_info',$lists);
        return $this->success();
    }



}