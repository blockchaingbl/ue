<?php
namespace App\Open\Services\App\User;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\LockTransferAuth;
use App\Http\Models\LockTransferLog;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class LocktransferApi extends FanweAuthService
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
    lock_time: 锁仓天数,
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
        if($param['type']==1){
            $lists = LockTransferLog::with('from_user')->where(function ($query)use ($user,$param){
                $query->where('to','=',$user->id);
            })->orderBy('id','desc')->paginate(10);
            $lists = $lists->map(function ($val)use($coin_type_all,$param){
                $val->coin_info = $coin_type_all[$val['coin_type']];
                $val->amount = Helper::formatCoin($val->amount,1);
                if(!$val->free){
                    if($val->sugar_type==0)
                    {
                        $diff = strtotime($val->lock_end_time)-time();
                        if($diff>0){
                            $day = ceil($diff/86400);
                            $val->desc = "{$day}天后可用";
                        }else{
                            $val->desc = '即将可用';
                        }
                    }else{
                        $val->free_amount = Helper::formatCoin($val->sugar_amount-$val->less_sugar_amount,1);
                        $val->desc = '已释放'.$val->free_amount;
                    }
                }else{
                    $val->desc = "已可用";
                }
                $val->lock_time =$val->lock_time;
                unset($val->sugar);
                return $val;
            })->toArray();

        }else {
            $lists = LockTransferLog::with('to_user')->where(function ($query)use ($user,$param){
                $query->where('from',$user->id);
            })->orderBy('id','desc')->paginate(10);
            $total = $lists->total();
            $this->setData('total',$total);
            $lists = $lists->map(function ($val)use ($coin_type_all){
                $val->free_amount = $val->amount - $val->less_amount;
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
     * @description 转账
     * @param
     * coin_type 币种
     * amount 数量
     * copys 份数
     * copys_amount 每份数量
     * lock_time 锁仓天数
     *  领取天数限制
     * lock_transfer_fee 手续费
     * @return
     * 成功：data:{
     * }
     * 失败：返回错误码及提示
     */
    public function grant($param)
    {
        if(!$param['amount']){
            return $this->error('请输入转账数量');
        }
        $param['lock_time'] = intval($param['lock_time']);
        if(!is_numeric($param['lock_time']) || $param['lock_time']<0){
            return $this->error('请输入锁仓时间');
        }

        $user = $this->user;
        $lock_transfer_fee = 0;
        if($param['lock_time']>0)
        {
            if(!is_numeric($param['start_day']))
            {
                return $this->error('请输入开始释放天数');
            }
            if($param['start_day']<1)
            {
                return $this->error('至少一天后开始释放');
            }
            $auth = LockTransferAuth::where('user_id',$user->id)->where('coin_type',$param['coin_type'])->first();
            if(!$auth || $auth->status!=1||$user->vc_total < $auth->min_limit){
                return $this->error('没有权限');
            }
        }else{
            if(!db_config('TRANSFER_OPEN'))
            {
                return $this->error('没有权限');
            }
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
            $over_limit = db_config('TRANSFER_OVER_LIMIT');
        }else{
            $plaprice = db_config('COIN_PRICE');
            $coin_type_price = CoinType::find($param['coin_type']);
            $rate =  $coin_type_price->price /$plaprice;
            $over_limit = $coin_type_price->transfer_over_limit;
        }
        if($param['lock_time']>=0)
        {
            $lock_transfer_fee = round($auth->receive_fee * $param['amount']*$rate,5);
            if($auth->auth_type==0)
            {
                if($param['sugar_type']=='day')
                {
                    $lock_day= $param['lock_time'];
                }elseif ($param['sugar_type']=='week')
                {
                    $lock_day = $param['lock_time']*7;
                }elseif ($param['sugar_type']=='month')
                {
                    $lock_day = (strtotime("+{$param['lock_time']} {$param['sugar_type']}") - time())/86400;
                }else{
                    return $this->error();
                }
                if($lock_day < $auth->limit_day)
                {
                    return $this->error("至少锁定{$auth->limit_day}天");
                }
            }elseif ($auth->auth_type==1)
            {
                $param['sugar_type'] = 'day';
                $param['lock_time'] = $auth->limit_day;
            }
        }
        else {
            $param['sugar_type'] = 'day';
        }

        if($param['coin_type']==0){
            if($param['vc_chose_type']=='vc_normal' && $user->vc_normal < $param['amount'])
            {
                return $this->error("可交易资产不足");
            }else if($param['vc_chose_type']=='vc_untrade' && $user->vc_untrade < $param['amount']){
                return $this->error("不可交易资产不足");
            }
            if($param['lock_time']!=0)
            {
                $need_total = $lock_transfer_fee + $param['amount'];
                if($need_total>$user->vc_total){
                    return $this->error("金额不足,含手续费需要{$need_total} ".db_config('COIN_UNIT'));
                }
            }
            else
            {
                $diff = $user->vc_total-$over_limit;
                if($param['amount']>$diff)
                {
                    return $this->error("超出{$over_limit}的部分才可以转账");
                }
            }

        }else{
            $user_asset = UserAsset::where('user_id',$user->id)->where('coin_type',$param['coin_type'])->first();
            if($param['lock_time']!=0)
            {
                if($user_asset->vc_total < $param['amount']){
                    return $this->error('金额不足');
                }
                if($user->vc_total < $lock_transfer_fee){
                    return $this->error("手续费不足");
                }
            }
            else
            {
                $diff = $user_asset->vc_total-$over_limit;
                if($param['amount']>$diff)
                {
                    return $this->error("超出{$over_limit}的部分才可以转账");
                }
            }
        }
        $to_user = User::where('mobile',$param['mobile'])->first();
        if(!$to_user){
            return $this->error("用户不存在,请确认手机号码");
        }
        if($to_user->id == $user->id){
            return $this->error("无法转账给自己");
        }
        try {
            DB::beginTransaction();
            $lock_tranfer_log = new LockTransferLog();
            $lock_tranfer_log->create_time = date('Y-m-d H:i:s');
            $lock_tranfer_log->from = $user->id;
            $lock_tranfer_log->to = $to_user->id;
            $lock_tranfer_log->coin_type = $param['coin_type'];
            if($param['coin_type']==0)
            {
                if($param['vc_chose_type']=='vc_normal')
                {
                    $lock_tranfer_log->vc_normal = $param['amount'];
                }else{
                    $lock_tranfer_log->vc_untrade = $param['amount'];
                }
            }else
            {
                $lock_tranfer_log->vc_normal = $param['amount'];
            }
            $lock_tranfer_log->amount = $param['amount'];
            $lock_tranfer_log->less_amount = $param['amount'];
            $lock_tranfer_log->lock_time = $param['lock_time'];
            $lock_tranfer_log->remain_time = $param['lock_time'];
            $lock_tranfer_log->sugar_type = $param['sugar_type'];
            $lock_tranfer_log->lock_end_time = date('Y-m-d H:i:s',strtotime("+{$param['lock_time']} {$param['sugar_type']}"));
            $lock_tranfer_log->lock_transfer_fee = $lock_transfer_fee;

            if($param['start_day']>1)
            {
                $param['start_day']-=1;
                $lock_tranfer_log->last_release_time = date('Y-m-d H:i:s',strtotime("+{$param['start_day']} day"));
            }else{

                $lock_tranfer_log->last_release_time = date('Y-m-d H:i:s');
            }

            if($param['lock_time']==0){
                $lock_tranfer_log->free = 1;
            }else{
                $lock_tranfer_log->free = 0;
            }
            $lock_tranfer_log->save();
            Helper::expendCoin('lock_transfer',$lock_tranfer_log->id);//转账支出
            if($lock_transfer_fee > 0)
            {
                Helper::expendCoin('lock_transfer_fee',$lock_tranfer_log->id);//转账手续费支出
            }
            Helper::incomeCoin('lock_transfer',$lock_tranfer_log->id);//转账收入
            if($lock_tranfer_log->lock_time!=0){
                $lock_tranfer_log->freeze_log_id = Helper::freezeCoin('lock_transfer',$lock_tranfer_log->id);//锁仓冻结
                $lock_tranfer_log->save();
            }
            if($param['lock_time']>0)
            {
                $auth->sugar_time++;
                $auth->sugar_total += $lock_tranfer_log->amount;
                $auth->save();
                $cp_amount = $lock_tranfer_log->amount*$auth->cp_rate;
                Helper::GrantCp($to_user->id,1,$cp_amount,'lock_transfer');
                if($to_user->pid>0)
                {
                    $invite_amount = $lock_tranfer_log->amount*$auth->invite_cp_rate;;
                    Helper::GrantCp($to_user->pid,1,$invite_amount,'lock_transfer_invite');
                }
            }


            DB::commit();
            $lock_tranfer_log->to_user_name = $to_user->username;
            $this->setData('sugar',$lock_tranfer_log);
            return $this->success();
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
        $lists = LockTransferLog::with('to_user')->where('sugar_id',$param['sugar_id'])->paginate(10);
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
    public function auth($parma)
    {
        $user = $this->user;
        $has_sec = boolval($user->security);
        $lists = CoinType::getAll();
        $assets = UserAsset::where('user_id',$user->id)->get()->keyBy('coin_type')->toArray();
        $auth_info = LockTransferAuth::where('user_id',$user->id)->get()->KeyBy('coin_type')->toArray();
        $lists = collect($lists)->map(function ($val,$key)use($assets,$auth_info,$user){
            if($assets[$key]){
                $val['assets'] = $assets[$key];
                $val['assets']['amount'] =  Helper::formatCoin($val['assets']['vc_total'],1);
            }else{
                $val['assets']['amount'] =  Helper::formatCoin(0.00000,1);
            }
            if($key==0)
            {
                $val['assets']['vc_untrade'] = Helper::trimNumber($user->vc_untrade,2);
                $val['assets']['vc_normal'] = Helper::trimNumber($user->vc_normal,2);
            }
            if($auth_info[$key]){
                $val['auth_info'] = $auth_info[$key];
            }else{
                $val['auth_info']['status'] = 0;
            }
            return $val;
        })->toArray();
        $this->setData('auth_info',$lists);
        $this->setData('has_sec',$has_sec);
        return $this->success();
    }



}