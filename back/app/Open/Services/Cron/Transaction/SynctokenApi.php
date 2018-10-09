<?php
namespace App\Open\Services\Cron\Transaction;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\BlockSynchronize;
use App\Http\Models\EthereumAccount;
use App\Http\Models\EthereumAddress;
use App\Http\Models\EthereumTokenTransaction;
use App\Http\Models\EthereumTransaction;
use App\Http\Models\InchargeLog;
use App\Http\Models\LockTransferLog;
use App\Http\Models\SubscribeToken;
use App\Http\Models\UserWallet;
use App\Http\Models\Web\User;
use App\Http\Models\Withdraw;
use App\Open\Services\FanweEthereumService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//区块代币交易同步接口，同步包括下载，确认，通知
class SynctokenApi extends FanweEthereumService
{

    /**
     * @name confirm
     * @description 确认交易，低于指定确认数的多次确认，多次保存
     * @param $param 无
     * @return array
     */
    public function confirm($param)
    {
        set_time_limit(0);
        $lock = Cache::get("cron_confirm_token");
        if(!$lock)
        {
            Cache::put("cron_confirm_token","locked",5); //锁住5分钟，即如果有异常的锁，5分钟后解开
            DB::beginTransaction();
            try{
                $tx_list = EthereumTokenTransaction::where("confirmed",0)->limit(5)->get();
                foreach($tx_list as $tx_item)
                {
                    $config_chains = config("app.block_chain");
                    $chain = $config_chains[$tx_item->block_chain];
                    $max_confirmations = $chain['ethereum_confirm_count'];  //达到的确认数

                    $this->ethereum->change_chain($tx_item->block_chain);
                    $block_height = BlockSynchronize::where("block_chain",$tx_item->block_chain)->first()->block_number;

                    //正常交易单
                    //确认交易数据
                    $tx_data = $this->ethereum->eth_getTransactionReceipt($tx_item->hash);
                    if(!$tx_data&&(time() - $tx_item->time_stamp)>7200)
                    {
                        //一小时内还没有被排队的交易被视为出错
                        $tx_item->is_error = 1;
                        $tx_item->confirmed = 1;
                        $tx_item->pending = 0;
                        $tx_item->save();
                        DB::commit();
                        Cache::forget("cron_confirm_token");
                        return $this->success();
                    }
                    $tx_block = hexdec($tx_data->blockNumber);
                    if(!$tx_block)
                    {
//                            DB::rollback();
//                            Cache::forget("cron_confirm_token");
//                            Log::warn("confirm_token_exception:tx_fetch_failed");
//                            return $this->error("tx_fetch_failed");
                        continue;  //继续等待
                    }
                    $tx_item->block_number =  $tx_block;

                    if($tx_block!=$tx_item->block_number)
                    {
                        $block_data = $this->ethereum->eth_getBlockByNumber($tx_data->blockNumber);
                        $tx_item->time_stamp = hexdec($block_data->timestamp);
                    }

                    $tx_item->contract_address = $tx_data->logs[0]->address?$tx_data->logs[0]->address:$tx_item->contract_address;
                    $tx_item->cumulative_gas_used = hexdec($tx_data->cumulativeGasUsed);
                    $tx_item->gas_used = hexdec($tx_data->gasUsed);

                    $token_info = SubscribeToken::where("contract_address",$tx_item->contract_address)->first();
                    $tx_item->token_name = $token_info->token_name;
                    $tx_item->token_symbol = $token_info->token_symbol;
                    $tx_item->token_decimal = $token_info->token_decimals;
                    $tx_item->confirmations = ($block_height - $tx_block)<=0?1:($block_height - $tx_block);
                    $tx_item->pending = 0;
                    if($tx_item->confirmations>=$max_confirmations) //已达到确认要求
                    {
                        //解析具体的Logs
                        if($tx_data->logs[0]->topics[0]!="0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef")
                        {
                            $tx_item->is_error = 1;
                        }
                        $tx_item->confirmed = 1;
                        $logs = $tx_data->logs;
                        $tx_item->logs = json_encode($logs);
                        $tx_item->save();
                        $send_status = $tx_item->is_error ? "error":"success";
                        Withdraw::where("tx_hash",$tx_item->hash)->update(["send_status"=>$send_status]);
                        //确认后二次查账
                        if(UserWallet::where("address",$tx_item->from)->first())
                        {
                            $ethereum_account = EthereumAccount::where(["address"=>$tx_item->from,"contract_address"=>$tx_item->contract_address,"block_chain"=>$tx_item->block_chain])->first();
                            if(!$ethereum_account){
                                $ethereum_account = new EthereumAccount();
                                $ethereum_account->address = $tx_item->from;
                                $ethereum_account->contract_address = $tx_item->contract_address;
                                $ethereum_account->block_chain = $tx_item->block_chain;
                            }
                            $ethereum_account->block_number = $tx_item->block_number;
                            $ethereum_account->value = $this->getErcBlanceWei($tx_item->contract_address,$tx_item->from);
                            $ethereum_account->save();
                        }
                        if(UserWallet::where("address",$tx_item->to)->first())
                        {
                            $ethereum_account = EthereumAccount::where(["address"=>$tx_item->to,"contract_address"=>$tx_item->contract_address,"block_chain"=>$tx_item->block_chain])->first();
                            if(!$ethereum_account){
                                $ethereum_account = new EthereumAccount();
                                $ethereum_account->address = $tx_item->to;
                                $ethereum_account->contract_address = $tx_item->contract_address;
                                $ethereum_account->block_chain = $tx_item->block_chain;
                            }
                            $ethereum_account->block_number = $tx_item->block_number;
                            $ethereum_account->value = $this->getErcBlanceWei($tx_item->contract_address,$tx_item->to);
                            $ethereum_account->save();
                        }
                    }
                    else
                    {
                        $tx_item->save();
                    }
                }
                DB::commit();
                Cache::forget("cron_confirm_token");
                return $this->success();
            }
            catch(\Exception $e)
            {
                DB::rollback();
                Cache::forget("cron_confirm_token");
                Log::warn("confirm_token_exception".$e->getMessage());
                return $this->error("confirm token exception:".$e->getMessage());
            }
        }
        else
        {
            return $this->error("confirm token locked");
        }
    }


    /**
     * @name push
     * @description
     * 将超过确认次数的交易
     * 推送出去
     * @param $param
     * @return array
     */
    public function push($param)
    {
        set_time_limit(0);
        $lock = Cache::get("cron_push_token");
        if(!$lock)
        {

            Cache::put("cron_push_token","locked",5); //锁住5分钟，即如果有异常的锁，5分钟后解开
            DB::beginTransaction();
            try{
                $tx_list = EthereumTokenTransaction::where(["confirmed"=>1,"push"=>1,"push_success"=>0,"is_error"=>0])->limit(5)->get();  //每次推送5条
                foreach($tx_list as $tx_item)
                {
                    if($tx_item->logs)
                    {
                        $logs = json_decode($tx_item->logs);
                    }
                    $json_data = json_decode(json_encode($tx_item));
                    $json_data->logs = $logs;
//                    $json_data = json_encode($json_data);
//                    $result = $this->httpEncPost($push_url,['data'=>$json_data,"confirm"=>1]);

                    //开始入账
                    $incharge_log = InchargeLog::where("tx_hash",$json_data->hash)->first();
                    $token_config = SubscribeToken::where(["contract_address"=>$json_data->contract_address,"block_chain"=>$tx_item->block_chain])->first();
                    if(!$incharge_log)
                    {
                        $incharge_wallet = UserWallet::where("address",$json_data->from)->first();
                        if($token_config&&$incharge_wallet)
                        {
                            $incharge_log = new InchargeLog();
                            $incharge_log->token_symbol = $token_config->token_symbol;
                            $incharge_log->token_amount =  Helper::trimNumber(floatval($json_data->value)/pow(10,$token_config->token_decimals),5);
                            if($incharge_log->token_symbol==db_config('COIN_UNIT'))
                            {
                                $incharge_log->token_amount = $incharge_log->token_amount*2;
                            }
                            $incharge_log->tx_hash = $json_data->hash;
                            $incharge_log->create_time = date("Y-m-d H:i:s",time());
                            $incharge_log->amount = $incharge_log->token_amount;
                            $incharge_log->user_id = $incharge_wallet->user_id;
                            $incharge_log->save();
                            Helper::incomeCoin("incharge",$incharge_log->id);
                            if($incharge_log->token_symbol==db_config('COIN_UNIT'))
                            {
                                $cp = $incharge_log->token_amount*db_config('IC');
                                Helper::GrantCp($incharge_wallet->user_id,1,$cp,'incharge');
                                $user  = User::find($incharge_wallet->user_id);
                                if($user->pid>0)
                                {
                                    $ic= $incharge_log->token_amount*db_config('IIC');
                                    Helper::GrantCp($user->pid,1,$ic,'incharge_invite');
                                }
                                $freeze_log_id = Helper::freezeCoin("incharge",$incharge_log->id);
                                $lockTransferLog = new LockTransferLog();
                                $lockTransferLog->from = 0;
                                $lockTransferLog->to = $incharge_wallet->user_id;
                                $lockTransferLog->coin_type =0;
                                $lockTransferLog->vc_normal =$incharge_log->amount;
                                $lockTransferLog->create_time = date('Y-m-d H:i:s');
                                $lockTransferLog->amount = $incharge_log->amount;
                                $lockTransferLog->less_amount = $incharge_log->amount;
                                $day =db_config('INCHARGE_LOCK_TIME');
                                $lockTransferLog->lock_time = $day;
                                $lockTransferLog->remain_time = $day;
                                $lockTransferLog->sugar_type = 'day';
                                $lockTransferLog->lock_end_time = date('Y-m-d H:i:s',strtotime("+{$day} day"));
                                $lockTransferLog->lock_transfer_fee = 0;
                                $lockTransferLog->last_release_time = date('Y-m-d H:i:s');
                                $lockTransferLog->freeze_log_id =$freeze_log_id;
                                $lockTransferLog->save();
                            }
                        }
                    }

                    $tx_item->push_count++;
                    $tx_item->push_success = 1;
                    $tx_item->save();
                }
                DB::commit();
                Cache::forget("cron_push_token");
                return $this->success();
            }
            catch(\Exception $e)
            {
                DB::rollback();
                Cache::forget("cron_push_token");
                Log::warn("push_token_exception".$e->getMessage());
                return $this->error("push token exception:".$e->getMessage());
            }
        }
        else
        {
            return $this->error("push token locked");
        }
    }

}