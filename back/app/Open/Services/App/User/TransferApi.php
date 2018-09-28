<?php
namespace App\Open\Services\App\User;

use App\Helper;
use App\Http\Models\ExRate;
use App\Http\Models\TransferOrder;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TransferApi extends FanweAuthService
{

    public function userinfo($param)
    {
        $user = User::select('id','username','mobile','avatar')->whereRaw("(mobile = '{$param['to']}' OR username = '{$param['to']}')")
        ->where('status',1)
        ->first();
        if($user)
        {
            $this->setData('user',$user);
            return $this->success();
        }else{
            return $this->error('用户不存在');
        }
    }

    public function orders($param)
    {
        $user = $this->user;
        if($param['type']==1)
        {
            $lists = TransferOrder::with('from_user')->where(function ($query)use ($user,$param){
                $query->where('to','=',$user->id);
            })->orderBy('id','desc')->paginate(10);
            $total = $lists->total();
            $this->setData('total',$total);
            $lists = $lists->map(function ($val){
                $val->receive = Helper::formatCoin($val->receive,5);
                return $val;
            })->toArray();
        }else{
            $lists = TransferOrder::with('to_user')->where(function ($query)use ($user,$param){
                $query->where('from','=',$user->id);
            })->orderBy('id','desc')->paginate(10);
            $total = $lists->total();
            $this->setData('total',$total);
            $lists = $lists->map(function ($val){
                $val->amount = Helper::formatCoin($val->amount,5);
                return $val;
            })->toArray();
        }


        $this->setData('orders',$lists);
        return $this->success();
    }

    public function send($param)
    {
        $user = $this->user;
        $amount = $param['amount'];
        $ex_id = $param['ex_id'];
        $to_id = $param['to_id'];
        if(!is_numeric($amount))
        {
            return $this->error('价格格式错误');
        }
        if(Helper::trimNumber($amount,2)!=$amount)
        {
            return $this->error('价格格式错误');
        }
        $ex = ExRate::where(['id'=>$ex_id])->where('open','1')->where('rate','>',0)->first();
        if(!$ex)
        {
            return $this->error('请选择币种');
        }
        $to_user = User::find($to_id);
        if(!$to_user)
        {
            return $this->error('请确认转账用户');
        }
        if(!$param['security'])
        {
            return $this->error('请输入资金密码');
        }
        if(md5($param['security'])!=$user->security)
        {
            return $this->error('资金密码错误');
        }
        $coin_price = floatval(db_config('COIN_PRICE'));
        $trans_fee = floatval(db_config('TRANS_FEE'));
//        $ex->rate = Helper::trimNumber($ex->rate,2);
        $pay_amount = round($amount*$ex->rate/$coin_price,5);
        $fee = round($trans_fee*$pay_amount,5);
        if($user->vc_normal<$pay_amount)
        {
            return $this->error('余额不足');
        }
        if($user->vc_normal<$pay_amount+$fee)
        {
            return $this->error('余额加上冲消费用不足');
        }
        $over_limit = db_config('TRANSFER_OVER_LIMIT');
        if($user->vc_normal<$pay_amount +$over_limit)
        {
            return $this->error("资产超出{$over_limit}部分才可以转账");
        }

        DB::beginTransaction();
        try {
            $trans_order = new TransferOrder();
            $trans_order->from = $user->id;
            $trans_order->to = $to_user->id;
            $trans_order->amount = $pay_amount+$fee;
            $trans_order->receive = $pay_amount;
            $trans_order->trans_fee = $fee;
            $trans_order->create_time = date('Y-m-d H:i:s');
            $trans_order->ex_id =  $ex_id;
            $trans_order->ex_rate =$ex->rate;
            $trans_order->currency = $amount;
            $trans_order->save();
            Helper::expendCoin('trans_order', $trans_order->id);
            Helper::incomeCoin('trans_order', $trans_order->id);
            $cp_amount = $trans_order->amount * db_config('TRANSFER_CP');
            Helper::GrantCp($user->id,1,$cp_amount,'transfer');
            if($user->pid>0)
            {
                $invite_cp_amount = $trans_order->amount * db_config('TRANSFER_INVITE');
                Helper::GrantCp($user->pid,1,$invite_cp_amount,'transfer_invite');
            }
            DB::commit();
            $this->setData('transfer',$trans_order);
            return $this->success();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::warn("transfer".$e->getMessage());
            return $this->error("transfer".$e->getMessage());
        }
    }

    public function userexrate($param)
    {
        $user = User::select('id','username','mobile','avatar')->where('id',$param['id'])->first();
        $login_user = $this->user;

        $lists = ExRate::where('open',1)->where('rate','>',0)->orderBy('sort','asc')->get()->toArray();
//        foreach ($lists as $key=>$val)
//        {
//            $lists[$key]['rate'] = Helper::trimNumber($lists[$key]['rate'],2);
//        }
        $trans_fee = db_config('TRANS_FEE');
        $coin_price = db_config('COIN_PRICE');
        if($user)
        {
            $this->setData('has_sec',boolval($login_user->security));
            $this->setData('vc_normal',$login_user->vc_normal);
            $this->setData('coin_price',$coin_price);
            $this->setData('user',$user);
            $this->setData('lists',$lists);
            $this->setData('trans_fee',$trans_fee);
            return $this->success();
        }else{
            return $this->error('用户不存在');
        }
    }
}