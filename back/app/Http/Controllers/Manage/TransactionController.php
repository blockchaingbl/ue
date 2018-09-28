<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\BlockSynchronize;
use App\Http\Models\FreezeLog;
use App\Http\Models\Otc;
use App\Http\Models\OtcOrder;
use App\Http\Models\PriceTrend;
use App\Http\Models\CoinType;
use App\Http\Models\EthereumAccount;
use App\Http\Models\EthereumAddress;
use App\Http\Models\EthereumBatchTransaction;
use App\Http\Models\EthereumTokenTransaction;
use App\Http\Models\EthereumTransaction;
use App\Http\Models\Manage\Admin;
use App\Http\Models\SubscribeToken;
use App\Http\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
include_once(base_path("app/Library/Ethereum/ethereum.php"));
class TransactionController extends AuthBaseController
{
    public function estimate(Request $request)
    {
        $from = $request->input("from");
        $id = $request->input("id");
        $withdraw_data = Withdraw::where(["id"=>$id,"status"=>1])->where("send_status","<>","success")->first();
        if(!$withdraw_data)
        {
            return $this->error("数据异常，不可打款");
        }

        if($withdraw_data->coin_type>0){
            $coin_type = CoinType::where("id",$withdraw_data->coin_type)->first();
            $token = SubscribeToken::where("id",$coin_type->token_id)->first();
        }
        else{
            $token = SubscribeToken::where("platform_coin",1)->first();
        }

        if(!$token){
            return $this->error("资产的区块链配置异常");
        }

        $contract_address = "0x";
        $to = $withdraw_data->to_address;
        if($token->token_type==1)
            $contract_address = "0x";
        elseif($token->token_type==2)
            $contract_address = $token->contract_address;
        $value = $request->input("value");

        if($withdraw_data->send_status=="pending"&&$withdraw_data->send_time!="0000-00-00 00:00:00"){
            $nonce = $withdraw_data->nonce;
        }
        else
        {
            $nonce = 0;
            $max_nonce_tx = Withdraw::where("from_address",$from)->orderBy("nonce","desc")->first();
            if( time() - strtotime($max_nonce_tx->send_time) < 3600 )
            {
                $nonce = $max_nonce_tx->nonce;
            }
            if(!$nonce){
                try {
                    $eth = new \Ethereum(config("app.rpc_server_host"), config("app.rpc_server_port"));
                    $eth->change_chain($token->block_chain);
                    $nonce = $eth->eth_getTransactionCount($from, "latest", true);
                }catch(\Exception $e)
                {
                    return $this->error("交易出错");
                }
            }
            else
            {
                $nonce+=1;
            }
        }
        $result = Helper::invoke("app.wallet/transaction/estimate_manage",["from"=>$from,"to"=>$to,"value"=>$value,"contract_address"=>$contract_address,"nonce"=>$nonce,"block_chain"=>$token->block_chain]);
        return $result;
    }

    public function send(Request $request)
    {
//        return $this->success();
        DB::beginTransaction();
        try{
            $raw = $request->input("raw");
            $id = $request->input("id");
            $from = strtolower($request->input("from"));
            $gas = $request->input("gas");
            $gas_price = $request->input("gas_price");
            $nonce = $request->input("nonce");
            $block_chain  = $request->input("block_chain");
            $result = Helper::invoke("app.wallet/transaction/sendraw",["raw"=>$raw,"block_chain"=>$block_chain]);
            if($result['errcode']==0)
            {
                $tx_hash = $result['data']['tx_hash'];
                if(!$tx_hash)
                {
                    DB::rollback();
                    Log::warn("交易失败");
                    return $this->error("交易失败");
                }

                $withdraw = Withdraw::where("id",$id)->first();
                $withdraw->tx_hash = $tx_hash;
                $withdraw->from_address = $from;
                $withdraw->gas = $gas;
                $withdraw->gas_price = $gas_price;
                $withdraw->nonce = $nonce;
                $withdraw->send_status = "pending";
                $withdraw->block_chain = $block_chain;
                $withdraw->send_time = date("Y-m-d H:i:s");
                $withdraw->save();

                DB::commit();
            }
            else
            {
                Log::warn($result['message']);
                DB::rollback();
            }
            return $result;
        }catch (\Exception $e)
        {
            Log::warn($e->getMessage());
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    //交易管理-挂单管理
    public function putorder(Request $request)
    {
        $keyword = trim($request->input("keyword"));
        $putorder_date = $request->input('putorder_date');
        $time_type = $request->input('time_type');
        if($time_type==1){
            $time_fields = 'create_time';
        }else{
            $time_fields = 'down_time';
        }
        if($putorder_date)
        {
            $userDateStr = explode('~',$putorder_date);
            $begin_date = $userDateStr[0];
            $end_date = $userDateStr[1];
            $param['putorder_date'] = $begin_date.'~'.$end_date;
            $param['time_type'] = $time_type;
        }
        if($keyword){
            $searchUid = DB::table('user')->whereRaw("username like'%".$keyword."%' or fanwe_user.mobile like '".$keyword."%'")->lists('id');
            $lists = DB::table('otc')
                ->whereIn('user_id',$searchUid)
                ->where(function ($query) use($begin_date,$end_date,$time_fields){
                    if($begin_date)
                    {
                        $query->where($time_fields,">",$begin_date." 00:00:00");
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<",$end_date." 23:59:59");
                    }
                })
                ->orderBy('id','desc')
                ->paginate(20);
        }else{
            $lists = DB::table('otc')
                ->orderBy('id','desc')
                ->where(function ($query) use($begin_date,$end_date,$time_fields){
                    if($begin_date)
                    {
                        $query->where($time_fields,">",$begin_date." 00:00:00");
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<",$end_date." 23:59:59");
                    }
                })
                ->paginate(20);
        }
        $uidArr = $lists->pluck('user_id')->unique();
        $userInfo = DB::table('user')->select('username','mobile','id')->whereIn('id',$uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');
        $lists->map(function ($list) use ($userInfo){
            $list->username = collect($userInfo->get($list->user_id))->get('username');
            $list->mobile = collect($userInfo->get($list->user_id))->get('mobile');
        });
        return view("manage.transaction.putorder",['lists'=>$lists,'keyword'=>$keyword,'param'=>$param]);
    }

    //交易管理-成交订单
    public function saleorder(Request $request)
    {
        $order_sn = trim($request->input("order_sn"));
        $keyword = trim($request->input("keyword"));
        $saleorder_date = $request->input('saleorder_date');
        $time_type = $request->input('time_type');
        $appeal_status = $request->input('appeal_status','all');
        $status = $request->input('status','all');
        $deal_memo = trim($request->input('deal_memo'));
        if($time_type==1){
            $time_fields = 'create_time';
        }else if($time_type==2){
            $time_fields = 'pay_time';
        }else if($time_type==3){
            $time_fields = 'send_time';
        }elseif($time_type==4){
            $time_fields = 'cancel_time';
        }
        if($saleorder_date)
        {
            $userDateStr = explode('~',$saleorder_date);
            $begin_date = $userDateStr[0];
            $end_date = $userDateStr[1];
            $param['saleorder_date'] = $begin_date.'~'.$end_date;
            $param['time_type'] = $time_type;
        }
        $param['appeal_status'] = $appeal_status;
        $param['status'] = $status;
        $param['deal_memo'] = $deal_memo;
        $param['order_sn'] = $order_sn;
        $order_sn = Helper::OrderSNtoID($order_sn);
        if($keyword){
            $searchUid = DB::table('user')->whereRaw("username like'%".$keyword."%' or fanwe_user.mobile like '".$keyword."%'")->lists('id');
            $lists = DB::table('otc_order')
                ->where(function ($query) use($begin_date,$end_date,$time_fields,$searchUid,$appeal_status,$status,$deal_memo,$order_sn){
                    if($begin_date)
                    {
                        $query->where($time_fields,">=",strtotime($begin_date." 00:00:00"));
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<=",strtotime($end_date." 23:59:59"));
                    }
                    if($searchUid){
                        $query->whereRaw('(buyer IN (?) OR seller IN (?))',[$searchUid,$searchUid]);
                    }
                    if($appeal_status!=='all'){
                        $query->where('appeal_status',$appeal_status);
                    }
                    if($status!=='all'){
                        $query->where('status',$status);
                    }
                    if($deal_memo){
                        $query->where('deal_memo','like',"%{$deal_memo}%");
                    }
                    if($order_sn){
                        $query->where('id',$order_sn);
                    }
                })
                ->orderBy('id','desc')
                ->paginate(20);
        }else{
            $lists = DB::table('otc_order')
                ->where(function ($query) use($begin_date,$end_date,$time_fields,$appeal_status,$status,$deal_memo,$order_sn){
                    if($begin_date)
                    {
                        $query->where($time_fields,">=",strtotime($begin_date." 00:00:00"));
                    }
                    if($end_date)
                    {
                        $query->where($time_fields,"<=",strtotime($end_date." 23:59:59"));
                    }
                    if($appeal_status!=='all'){
                        $query->where('appeal_status',$appeal_status);
                    }
                    if($status!=='all'){
                        $query->where('status',$status);
                    }
                    if($deal_memo){
                        $query->where('deal_memo','like',"%{$deal_memo}%");
                    }
                    if($order_sn){
                        $query->where('id',$order_sn);
                    }
                })
                ->orderBy('id','desc')
                ->paginate(20);
        }
        $uidArr = $lists->map(function ($list){
            return [$list->seller,$list->buyer];
        })->collapse()->unique()->toArray();

        $userInfo = DB::table('user')->select('username','mobile','id')->whereIn('id',$uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');
        $lists->map(function ($list) use ($userInfo){
            $list->buyer = $userInfo->get($list->buyer);
            $list->seller = $userInfo->get($list->seller);
            $list->pay_info = json_decode($list->payment_info);
        });

        return view("manage.transaction.saleorder",['lists'=>$lists,'keyword'=>$keyword,'param'=>$param]);
    }

    //交易管理-下架操作
    public function down(Request $request)
    {
        $id = $request->input('id');
        $otc = Otc::find($id);
        if(!$otc){
            return $this->error('订单不存在',FanweErrcode::OTC_ID_NOT_EXSITS);
        }elseif ($otc->status!=1){
            return $this->error('只有上架订单可以下架',FanweErrcode::SYSTEM_ERROR);
        }
        $count = OtcOrder::where(['otc_id'=>$id])->whereIn('status',[0,1])->count();
        if($count>0)
        {
            return $this->error('有未完成的订单,无法下架');
        }
        try{
            DB::beginTransaction();
            $freeze_log = FreezeLog::where(['type'=>'sale','relate'=>$id])->first();
            $otc->status = 0;
            $otc->down_time = date('Y-m-d H:i:s');
            $otc->save();
            Helper::freeCoin($freeze_log->id);
            DB::commit();
            return $this->success();
        }   catch (\Exception $e)
        {
            DB::rollback();
            return $this->error('操作失败,请确认用户剩余冻结金额',FanweErrcode::SYSTEM_ERROR);
        }
    }

    //申诉处理
    public function appeal(Request $request)
    {
         $order_id = $request->input('id');
         $type = $request->input('type');
         //处理买家申诉 发币
         if($type==1){
             if(!$order_id)
             {
                 return $this->error("请输入订单ID",FanweErrcode::OTC_ORDER_ID_NOT_EXSITS);
             }
             $otc_order = OtcOrder::where(["id"=>$order_id])->first();
             if(!$otc_order)
             {
                 return $this->error("订单不存在",FanweErrcode::OTC_ORDER_NOT_EXSITS);
             }
             if($otc_order->status==1){
                 DB::beginTransaction();
                 try{
                     $otc_order->send_time = time();
                     $otc_order->status = 2;
                     $otc_order->save();
                     //生成卖家支出
                     Helper::expendCoin("sale",$otc_order->id);
                     //生成买家收入
                     Helper::incomeCoin("purchase",$otc_order->id);
                     //锁仓冻结
                     if($otc_order->is_lock && $otc_order->lock_day>0)
                     {
                         $otc_order->freeze_log_id = Helper::freezeCoin('otc_lock',$otc_order->id);
                     }
                     $otc_order->save();
                     $otc_info = Otc::where('id',$otc_order->otc_id)->first();
                     if($otc_info->vc_less_amount<db_config('MIN_OTC_BUY') && $otc_info->vc_less_amount>0){
                         $otc_info->status = 0;
                         $otc_info->down_time = date("Y-m-d H:i:s",time());
                         $otc_info->save();
                         //解冻剩余金额
                         $freeze_id = FreezeLog::where(["relate"=>$otc_info->id,"type"=>"sale"])->first()->id;
                         Helper::freeCoin($freeze_id);
                     }
                     elseif($otc_info->vc_less_amount==0)
                     {
                         //全部售完，下架
                         $otc_info->status = 0;
                         $otc_info->down_time = date("Y-m-d H:i:s",time());
                         $otc_info->save();
                         $freeze_log= FreezeLog::where(["relate"=>$otc_info->id,"type"=>"sale"])->first();
                         if($freeze_log->vc_amount - $freeze_log->vc_done_amount - $freeze_log->vc_lock_normal > 0)
                         {
                             Helper::freeCoin($freeze_log->id);
                         }
                     }
                     //生成最新价格
                     $price_trend = new PriceTrend();
                     $price_trend->price = $otc_order->vc_uint_price;
                     $price_trend->create_time = date("Y-m-d H:i:s");
                     $price_trend->save();
                     //累计卖家违约次数
                     Helper::userBreakCount($otc_order->seller);
                     $update_data = array_merge(['appeal_status'=>3],$request->only('deal_memo'));
                     OtcOrder::where('id',$order_id)->update($update_data);
                     DB::commit();
                     return $this->success();
                 }
                 catch (\Exception $e)
                 {
                     DB::rollback();
                     return $this->error($e->getMessage());
                 }
             }else{
                 $update_data = array_merge(['appeal_status'=>3],$request->only('deal_memo'));
                 OtcOrder::where('id',$order_id)->update($update_data);
                 return $this->success();
             }

         }
         //处理卖家申诉 取消订单
         elseif($type==2){
             if(!$order_id)
             {
                 return $this->error("请输入订单ID",FanweErrcode::OTC_ORDER_ID_NOT_EXSITS);
             }

             $otc_order = OtcOrder::where(["id"=>$order_id,"status"=>1])->first();
             if(!$otc_order)
             {
                 return $this->error("订单不存在",FanweErrcode::OTC_ORDER_NOT_EXSITS);
             }
             DB::beginTransaction();
             try{
                 $otc_order->status = 3;
                 $otc_order->cancel_time = time();
                 $otc_order->save();
                 //解锁金额
                 $otc = Otc::where(["id"=>$otc_order->otc_id])->first();
                 $freeze_id = FreezeLog::where(["relate"=>$otc->id,'type'=>'sale'])->first()->id;
                 Helper::unlockCoin($freeze_id,$otc_order->vc_amount + $otc_order->vc_fee);
                 //返还otc挂卖交易单的剩余数量
                 $otc->vc_less_amount += $otc_order->vc_amount;
                 $otc->vc_less_fee += $otc_order->vc_fee;

                 $otc->save();
                 //累计买家违约次数
                 Helper::userBreakCount($otc_order->buyer);
                 $update_data = array_merge(['appeal_status'=>3],$request->only('deal_memo'));
                 OtcOrder::where('id',$order_id)->update($update_data);
                 DB::commit();
                 return $this->success();
             }
             catch (\Exception $e)
             {
                 DB::rollback();
                 return $this->error($e->getMessage());
             }
         }
    }


}
