<?php
namespace App\Open\Services\Cron\Transaction;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\BlockSynchronize;
use App\Http\Models\EthereumAccount;
use App\Http\Models\EthereumTokenTransaction;
use App\Http\Models\EthereumTransaction;
use App\Http\Models\InchargeLog;
use App\Http\Models\SubscribeToken;
use App\Http\Models\UserSubscribeToken;
use App\Http\Models\UserWallet;
use App\Http\Models\Withdraw;
use App\Open\Services\FanweEthereumService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//区块交易同步接口，同步包括下载，确认，通知
class SyncblockApi extends FanweEthereumService
{

    private $block_chain;

    /**
     * @name downblock
     * @description 直接下载区块中相关的交易，只支持token_add:true
     * @param $param
     * @return array
     */
    public function downblock($param){
        set_time_limit(0);

        $block_chain = $param['block_chain'];
        $config_chains = config("app.block_chain");
        if(!$config_chains[$block_chain])$block_chain = "platform";
        $this->block_chain = $block_chain;

        $lock = Cache::get("cron_block".$block_chain);
        if(!$lock)
        {
            Cache::put("cron_block".$block_chain,true,1);
            DB::BeginTransaction();
            try{
                $this->ethereum->change_chain($block_chain);
                $curl_count = 0;
                $last_block_sync = BlockSynchronize::where("block_chain",$block_chain)->first();
                $block = $last_block_sync->block_number;
                $chain_last_block = $this->ethereum->eth_blockNumber(true);
                $curl_count++;
                if($block<$chain_last_block&&$last_block_sync->tx_index==0)
                    $block++;

                $hex_block = "0x".dechex($block);
                $block_data = Cache::get("block".$block_chain.$hex_block);
                if($block_data)
                {
                    $block_data = json_decode($block_data);
                }
                else
                {
                    $block_data = $this->ethereum->eth_getBlockByNumber($hex_block);
                    $curl_count++;
                    if($block_data)
                        Cache::put("block".$block_chain.$hex_block,json_encode($block_data),2);
                }

                $txs = $block_data->transactions;

                for($i=$last_block_sync->tx_index;$i<count($txs);$i++){
                    $tx = $txs[$i];
                    if($tx)
                    {
                        $from = $tx->from;
                        $to = $tx->to;
                        $hash = $tx->hash;
                        $save_tx = false;
                        //每判定普通交易的入账
                        if(UserWallet::where("address",$from)->first())
                        {
                            $ethereum_account = EthereumAccount::where(["address"=>$from,"contract_address"=>"0x","block_chain"=>$block_chain])->first();
                            if(!$ethereum_account){
                                $ethereum_account = new EthereumAccount();
                                $ethereum_account->address = $from;
                                $ethereum_account->contract_address = "0x";
                                $ethereum_account->block_chain = $block_chain;
                            }
                            $ethereum_account->block_number = $block;
                            $ethereum_account->value = $this->getEtherBalanceWei($from);
                            $curl_count++;
                            $ethereum_account->save();
                            $save_tx = true;
                        }
                        if(UserWallet::where("address",$to)->first())
                        {
                            $ethereum_account = EthereumAccount::where(["address"=>$to,"contract_address"=>"0x","block_chain"=>$block_chain])->first();
                            if(!$ethereum_account){
                                $ethereum_account = new EthereumAccount();
                                $ethereum_account->address = $to;
                                $ethereum_account->contract_address = "0x";
                                $ethereum_account->block_chain = $block_chain;
                            }
                            $ethereum_account->block_number = $block;
                            $ethereum_account->value = $this->getEtherBalanceWei($to);
                            $curl_count++;
                            $ethereum_account->save();
                            $save_tx = true;
                        }
                        if($save_tx)
                        {
                            $tx->timeStamp = $block_data->timestamp;
                            $this->saveTx($tx);
                        }

                        $subscribe_token = SubscribeToken::where(["contract_address"=>$to,"block_chain"=>$block_chain])->first();



                        //开始解析input
                        $input = strtolower($tx->input);
                        if(substr($input,0,10)=='0xa9059cbb'){ //转账交易，生成token关联，只处理transafer交易

                            $contract_address = $to;  //收款地址为合约地址
                            $input = str_replace("0xa9059cbb","",$input);
                            $receiver = "0x".substr(substr($input,0,64),-40,40);
                            $token_value = '0x'.ltrim(substr($input,64,64),'0');

                            //$token_value = hexdec($token_value);

                            $token_info = null;
                            $from_wallet = UserWallet::where("address",$from)->first();
                            $to_wallet = UserWallet::where("address",$receiver)->first();

                            if($from_wallet||$to_wallet){
                                if(!$subscribe_token)
                                {
                                    $token_info = $this->getErcContract($contract_address);
                                    $curl_count++;
                                    if($token_info['symbol']&&$token_info['name'])
                                    {
                                        $subscribe_token = new SubscribeToken();
                                        $subscribe_token->contract_address = $contract_address;
                                        $token_symbol = SubscribeToken::where("token_symbol","like","%".$token_info['symbol']."%")->count();
                                        if($token_symbol==0)
                                            $token_symbol = '';
                                        $subscribe_token->token_symbol = $token_info['symbol'].$token_symbol;
                                        $token_name = SubscribeToken::where("token_name","like","%".$token_info['name']."%")->count();
                                        if($token_name==0)
                                            $token_name = '';
                                        $subscribe_token->token_name = $token_info['name'].$token_name;
                                        $subscribe_token->token_decimals = $token_info['decimals'];
                                        $subscribe_token->syn_count = 0;
                                        $subscribe_token->token_type = 2;
                                        $subscribe_token->block_chain = $block_chain;
                                        $subscribe_token->save();
                                    }
                                }
                            }

                            if($subscribe_token)
                            {

                                /*
                                $contract_address = $to;  //收款地址为合约地址
                                $from = "";
                                $to = "";
                                //查询收款人
                                $ethereum_tx = $this->ethereum->eth_getTransactionReceipt($hash);
                                $curl_count++;
                                foreach($ethereum_tx->logs as $log)
                                {
                                    if(strtolower($log->topics[0])=="0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef") //Transfer事件
                                    {
                                        $from = "0x".substr($log->topics[1],-40,40);
                                        $to = "0x".substr($log->topics[2],-40,40);
                                        $token_value = "0x".ltrim($log->data,"0");
                                        break;
                                    }
                                }

                                $from_wallet = UserWallet::where("address",$from)->first();
                                $to_wallet = UserWallet::where("address",$to)->first();
                                */

                                $to = $receiver;
                                $save_tx = false;
                                if($from_wallet)
                                {
                                    $ethereum_account = EthereumAccount::where(["address"=>$from,"contract_address"=>$contract_address,"block_chain"=>$block_chain])->first();
                                    if(!$ethereum_account){
                                        $ethereum_account = new EthereumAccount();
                                        $ethereum_account->address = $from;
                                        $ethereum_account->contract_address = $contract_address;
                                        $ethereum_account->block_chain = $block_chain;
                                    }
                                    $ethereum_account->block_number = $block;
                                    $ethereum_account->value = $this->getErcBlanceWei($contract_address,$from);
                                    $curl_count++;
                                    $ethereum_account->save();
                                    $save_tx = true;

                                    //关联user_subscribe_token
                                    $user_subscribe_token  = UserSubscribeToken::where(["user_id"=>$from_wallet->user_id,"token_id"=>$subscribe_token->id])->first();
                                    if(!$user_subscribe_token){
                                        $user_subscribe_token = new UserSubscribeToken();
                                        $user_subscribe_token->user_id = $from_wallet->user_id;
                                        $user_subscribe_token->token_id = $subscribe_token->id;
                                    }
                                    $user_subscribe_token->status = 1;
                                    $user_subscribe_token->save();

                                }
                                if($to_wallet)
                                {
                                    $ethereum_account = EthereumAccount::where(["address"=>$to,"contract_address"=>$contract_address,"block_chain"=>$block_chain])->first();
                                    if(!$ethereum_account){
                                        $ethereum_account = new EthereumAccount();
                                        $ethereum_account->address = $to;
                                        $ethereum_account->contract_address = $contract_address;
                                        $ethereum_account->block_chain = $block_chain;
                                    }
                                    $ethereum_account->value = $this->getErcBlanceWei($contract_address,$to);
                                    $curl_count++;
                                    $ethereum_account->block_number = $block;
                                    $ethereum_account->save();
                                    $save_tx = true;

                                    //关联user_subscribe_token
                                    $user_subscribe_token  = UserSubscribeToken::where(["user_id"=>$to_wallet->user_id,"token_id"=>$subscribe_token->id])->first();
                                    if(!$user_subscribe_token){
                                        $user_subscribe_token = new UserSubscribeToken();
                                        $user_subscribe_token->user_id = $to_wallet->user_id;
                                        $user_subscribe_token->token_id = $subscribe_token->id;
                                    }
                                    $user_subscribe_token->status = 1;
                                    $user_subscribe_token->save();
                                }

                                if($save_tx)
                                {
                                    $tx->timeStamp = $block_data->timestamp;
                                    $tx->value = $token_value;
                                    $tx->from = $from;
                                    $tx->to = $to;
                                    $tx->contractAddress = $contract_address;
                                    $this->saveTokenTx($tx);
                                }
                            }
                        }


                        if($curl_count>=20)
                        {
                            if($i+1<count($txs))
                                $last_block_sync->tx_index = $i+1;
                            else
                                $last_block_sync->tx_index = 0;
                            break;
                        }
                        else
                        {
                            $last_block_sync->tx_index = 0;
                        }
                    }
                    else
                    {
                        $last_block_sync->tx_index = 0;
                    }
                }

                if($last_block_sync->tx_index>=count($txs))
                {
                    $last_block_sync->tx_index = 0;
                }

                $last_block_sync->block_number = $block;
                $last_block_sync->save();

                Cache::forget("cron_block".$block_chain);
                DB::commit();
                return $this->success();
            }//end try
            catch(\Exception $e)
            {
                DB::rollback();
                Cache::forget("cron_block".$block_chain);
                Log::warn("cron_block:exception:".$e->getMessage());
                return $this->error("cron block:exception:".$block_chain.$e->getMessage());
            }
        }
        else
        {
            return $this->error("locked");
        }
    }


    public function repairblock($param){
        set_time_limit(0);

        $block_chain = $param['block_chain'];
        $config_chains = config("app.block_chain");
        if(!$config_chains[$block_chain])$block_chain = "platform";
        $this->block_chain = $block_chain;

        $last_block_sync = BlockSynchronize::where("block_chain",$block_chain)->first();
        if($last_block_sync->repair_block_number>=$last_block_sync->end_block_number)
        {
            return $this->success("repair done");
        }
        $lock = Cache::get("repair_block".$block_chain);
        if(!$lock)
        {
            Cache::put("repair_block".$block_chain,true,1);
            DB::BeginTransaction();
            try{
                $this->ethereum->change_chain($block_chain);
                $curl_count = 0;
                $block = $last_block_sync->repair_block_number;
                $chain_last_block = $this->ethereum->eth_blockNumber(true);
                $curl_count++;
                if($block<$chain_last_block&&$last_block_sync->repair_tx_index==0)
                    $block++;

                $hex_block = "0x".dechex($block);
                $block_data = Cache::get("block".$block_chain.$hex_block);
                if($block_data)
                {
                    $block_data = json_decode($block_data);
                }
                else
                {
                    $block_data = $this->ethereum->eth_getBlockByNumber($hex_block);
                    $curl_count++;
                    if($block_data)
                        Cache::put("block".$block_chain.$hex_block,json_encode($block_data),2);
                }


                $txs = $block_data->transactions;
                for($i=$last_block_sync->repair_tx_index;$i<count($txs);$i++){
                    $tx = $txs[$i];
                    if($tx)
                    {
                        $from = $tx->from;
                        $to = $tx->to;
                        $hash = $tx->hash;
                        $save_tx = false;
                        //每判定普通交易的入账
                        if(UserWallet::where("address",$from)->first())
                        {
                            $ethereum_account = EthereumAccount::where(["address"=>$from,"contract_address"=>"0x","block_chain"=>$block_chain])->first();
                            if(!$ethereum_account){
                                $ethereum_account = new EthereumAccount();
                                $ethereum_account->address = $from;
                                $ethereum_account->contract_address = "0x";
                                $ethereum_account->block_chain = $block_chain;
                            }
                            $ethereum_account->block_number = $block;
                            $ethereum_account->value = $this->getEtherBalanceWei($from);
                            $curl_count++;
                            $ethereum_account->save();
                            $save_tx = true;
                        }
                        if(UserWallet::where("address",$to)->first())
                        {
                            $ethereum_account = EthereumAccount::where(["address"=>$to,"contract_address"=>"0x","block_chain"=>$block_chain])->first();
                            if(!$ethereum_account){
                                $ethereum_account = new EthereumAccount();
                                $ethereum_account->address = $to;
                                $ethereum_account->contract_address = "0x";
                                $ethereum_account->block_chain = $block_chain;
                            }
                            $ethereum_account->block_number = $block;
                            $ethereum_account->value = $this->getEtherBalanceWei($to);
                            $curl_count++;
                            $ethereum_account->save();
                            $save_tx = true;
                        }
                        if($save_tx)
                        {
                            $tx->timeStamp = $block_data->timestamp;
                            $this->saveTx($tx);
                        }

                        $subscribe_token = SubscribeToken::where(["contract_address"=>$to,"block_chain"=>$block_chain])->first();

                        //开始解析input
                        $input = strtolower($tx->input);
                        if(substr($input,0,10)=='0xa9059cbb'){ //转账交易，生成token关联
                            $contract_address = $to;  //收款地址为合约地址
                            $input = str_replace("0xa9059cbb","",$input);
                            $receiver = "0x".substr($input,-104,40);
                            $token_value = '0x'.ltrim(substr($input,-64,64),'0');
                            //$token_value = hexdec($token_value);

                            $token_info = null;
                            $from_wallet = UserWallet::where("address",$from)->first();
                            $to_wallet = UserWallet::where("address",$receiver)->first();

                            if($from_wallet||$to_wallet){
                                if(!$subscribe_token)
                                {
                                    $token_info = $this->getErcContract($contract_address);
                                    $curl_count++;
                                    if($token_info['symbol']&&$token_info['name'])
                                    {
                                        $subscribe_token = new SubscribeToken();
                                        $subscribe_token->contract_address = $contract_address;
                                        $token_symbol = SubscribeToken::where("token_symbol","like","%".$token_info['symbol']."%")->count();
                                        if($token_symbol==0)
                                            $token_symbol = '';
                                        $subscribe_token->token_symbol = $token_info['symbol'].$token_symbol;
                                        $token_name = SubscribeToken::where("token_name","like","%".$token_info['name']."%")->count();
                                        if($token_name==0)
                                            $token_name = '';
                                        $subscribe_token->token_name = $token_info['name'].$token_name;
                                        $subscribe_token->token_decimals = $token_info['decimals'];
                                        $subscribe_token->syn_count = 0;
                                        $subscribe_token->token_type = 2;
                                        $subscribe_token->block_chain = $block_chain;
                                        $subscribe_token->save();
                                    }
                                }
                            }

                            if($subscribe_token)
                            {
                                /*
                                $contract_address = $to;  //收款地址为合约地址
                                $from = "";
                                $to = "";
                                //查询收款人
                                $ethereum_tx = $this->ethereum->eth_getTransactionReceipt($hash);
                                $curl_count++;
                                foreach($ethereum_tx->logs as $log)
                                {
                                    if(strtolower($log->topics[0])=="0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef") //Transfer事件
                                    {
                                        $from = "0x".substr($log->topics[1],-40,40);
                                        $to = "0x".substr($log->topics[2],-40,40);
                                        $token_value = "0x".ltrim($log->data,"0");
                                        break;
                                    }
                                }


                                $from_wallet = UserWallet::where("address",$from)->first();
                                $to_wallet = UserWallet::where("address",$to)->first();
                                */

                                $to = $receiver;
                                $save_tx = false;
                                if($from_wallet)
                                {
                                    $ethereum_account = EthereumAccount::where(["address"=>$from,"contract_address"=>$contract_address,"block_chain"=>$block_chain])->first();
                                    if(!$ethereum_account){
                                        $ethereum_account = new EthereumAccount();
                                        $ethereum_account->address = $from;
                                        $ethereum_account->contract_address = $contract_address;
                                        $ethereum_account->block_chain = $block_chain;
                                    }
                                    $ethereum_account->block_number = $block;
                                    $ethereum_account->value = $this->getErcBlanceWei($contract_address,$from);
                                    $curl_count++;
                                    $ethereum_account->save();
                                    $save_tx = true;

                                    //关联user_subscribe_token
                                    $user_subscribe_token  = UserSubscribeToken::where(["user_id"=>$from_wallet->user_id,"token_id"=>$subscribe_token->id])->first();
                                    if(!$user_subscribe_token){
                                        $user_subscribe_token = new UserSubscribeToken();
                                        $user_subscribe_token->user_id = $from_wallet->user_id;
                                        $user_subscribe_token->token_id = $subscribe_token->id;
                                    }
                                    $user_subscribe_token->status = 1;
                                    $user_subscribe_token->save();
                                }
                                if($to_wallet)
                                {
                                    $ethereum_account = EthereumAccount::where(["address"=>$to,"contract_address"=>$contract_address,"block_chain"=>$block_chain])->first();
                                    if(!$ethereum_account){
                                        $ethereum_account = new EthereumAccount();
                                        $ethereum_account->address = $to;
                                        $ethereum_account->contract_address = $contract_address;
                                        $ethereum_account->block_chain = $block_chain;
                                    }
                                    $ethereum_account->value = $this->getErcBlanceWei($contract_address,$to);
                                    $curl_count++;
                                    $ethereum_account->block_number = $block;
                                    $ethereum_account->save();
                                    $save_tx = true;

                                    //关联user_subscribe_token
                                    $user_subscribe_token  = UserSubscribeToken::where(["user_id"=>$to_wallet->user_id,"token_id"=>$subscribe_token->id])->first();
                                    if(!$user_subscribe_token){
                                        $user_subscribe_token = new UserSubscribeToken();
                                        $user_subscribe_token->user_id = $to_wallet->user_id;
                                        $user_subscribe_token->token_id = $subscribe_token->id;
                                    }
                                    $user_subscribe_token->status = 1;
                                    $user_subscribe_token->save();
                                }

                                if($save_tx)
                                {
                                    $tx->timeStamp = $block_data->timestamp;
                                    $tx->value = $token_value;
                                    $tx->from = $from;
                                    $tx->to = $to;
                                    $tx->contractAddress = $contract_address;
                                    $this->saveTokenTx($tx);
                                }
                            }

                        }





                        if($curl_count>=20)
                        {
                            if($i+1<count($txs))
                                $last_block_sync->repair_tx_index = $i+1;
                            else
                                $last_block_sync->repair_tx_index = 0;
                            break;
                        }
                        else
                        {
                            $last_block_sync->repair_tx_index = 0;
                        }
                    }else{
                        $last_block_sync->repair_tx_index = 0;
                    }
                }

                if($last_block_sync->repair_tx_index>=count($txs))
                {
                    $last_block_sync->repair_tx_index = 0;
                }

                $last_block_sync->repair_block_number = $block;
                $last_block_sync->save();

                Cache::forget("repair_block".$block_chain);
                DB::commit();
                return $this->success();
            }//end try
            catch(\Exception $e)
            {
                DB::rollback();
                Cache::forget("repair_block".$block_chain);
                Log::warn("repair_block:exception:".$e->getMessage());
                return $this->error("cron repair_block:exception:".$block_chain.$e->getMessage());
            }
        }
        else
        {
            return $this->error("locked");
        }
    }

    private function saveTx($tx_item)
    {
        $ethereum_tx = EthereumTransaction::where(["hash"=>$tx_item->hash,"block_chain"=>$this->block_chain])->first();
        if(!$ethereum_tx)
        {
            $ethereum_tx = new EthereumTransaction();
            $ethereum_tx->confirmations = 1;
            $ethereum_tx->block_chain = $this->block_chain;
        }

        //地址为以太币充值地址
        if(SubscribeToken::where(["token_type"=>1,"incharge_address"=>$tx_item->to,"block_chain"=>$this->block_chain])->first())
        {
            $ethereum_tx->push = 1;
        }

        $ethereum_tx->block_number = hexdec($tx_item->blockNumber);
        $ethereum_tx->time_stamp = hexdec($tx_item->timeStamp);
        $ethereum_tx->hash = $tx_item->hash;
        $ethereum_tx->nonce = hexdec($tx_item->nonce);
        $ethereum_tx->block_hash = $tx_item->blockHash;
        $ethereum_tx->transaction_index = hexdec($tx_item->transactionIndex);
        $ethereum_tx->from = $tx_item->from;
        $ethereum_tx->to = $tx_item->to;
        $ethereum_tx->value = hexdec($tx_item->value);
        $ethereum_tx->gas = hexdec($tx_item->gas);
        $ethereum_tx->gas_price = hexdec($tx_item->gasPrice);
        $ethereum_tx->input = $tx_item->input;
        $ethereum_tx->save();

        //将其他nonce相同的单子更新为is_error=1
        EthereumTransaction::where(["from"=>$ethereum_tx->from,"nonce"=>$ethereum_tx->nonce,"block_number"=>0])->update(["is_error"=>1,"confirmed"=>1,"confirmations"=>0,"pending"=>0]);
    }


    private function saveTokenTx($tx_item)
    {
        $ethereum_tx = EthereumTokenTransaction::where(["hash"=>$tx_item->hash,"block_chain"=>$this->block_chain])->first();
        if(!$ethereum_tx)
        {
            $ethereum_tx = new EthereumTokenTransaction();
            $ethereum_tx->confirmations = 1;
            $ethereum_tx->block_chain = $this->block_chain;
        }

        //地址为代币充值地址
        if(SubscribeToken::where(["contract_address"=>$tx_item->contractAddress,"incharge_address"=>$tx_item->to,"block_chain"=>$this->block_chain])->first())
        {
            $ethereum_tx->push = 1;
        }

        $ethereum_tx->block_number = hexdec($tx_item->blockNumber);
        $ethereum_tx->time_stamp = hexdec($tx_item->timeStamp);
        $ethereum_tx->hash = $tx_item->hash;
        $ethereum_tx->nonce = hexdec($tx_item->nonce);
        $ethereum_tx->block_hash = $tx_item->blockHash;
        $ethereum_tx->transaction_index = hexdec($tx_item->transactionIndex);
        $ethereum_tx->from = $tx_item->from;
        $ethereum_tx->to = $tx_item->to;
        $ethereum_tx->value = hexdec($tx_item->value);
        $ethereum_tx->gas = hexdec($tx_item->gas);
        $ethereum_tx->gas_price = hexdec($tx_item->gasPrice);
        $ethereum_tx->input = $tx_item->input;
        $ethereum_tx->contract_address = $tx_item->contractAddress;

        $ethereum_tx->save();

        //将其他nonce相同的单子更新为is_error=1
        EthereumTokenTransaction::where(["from"=>$ethereum_tx->from,"nonce"=>$ethereum_tx->nonce,"block_number"=>0])->update(["is_error"=>1,"confirmed"=>1,"confirmations"=>0,"pending"=>0]);

    }

    /**
     * 提现记录的结果轮询
     */
    public function withdraw($param){
        set_time_limit(0);
        $lock = Cache::get("withdraw_queue");
        if(!$lock)
        {
            Cache::put("withdraw_queue",true,5);
            try{
                //更新提现记录
                $withdraw = Withdraw::where("tx_hash","<>","")->where("send_status","pending")->orderBy('id','desc')->first();
                if($withdraw)
                {
                    $this->ethereum->change_chain($withdraw->block_chain);
                    $pend_tx = $this->ethereum->eth_getTransactionByHash($withdraw->tx_hash);
                    if(!$pend_tx)
                    {
                        $withdraw->send_status = "error";
                        $withdraw->save();
                    }
                    else{
                        $tx = $this->ethereum->eth_getTransactionReceipt($withdraw->tx_hash);
                        if($tx&&$tx->blockNumber){
                            $withdraw->block_number = hexdec($tx->blockNumber);
                            $withdraw->send_status = "success";
                            $withdraw->confirm_time = date("Y-m-d H:i:s");
                            $withdraw->save();
                        }
                    }

                }
                Cache::forget("withdraw_queue");
            }catch(\Exception $e){
                Cache::forget("withdraw_queue");
            }
            return 'success';
        }
        else{
            return "locked";
        }

    }

    //修复解码错误的token交易
    public function repairdecodeerror()
    {
        $lists = EthereumTokenTransaction::whereRaw('LENGTH(`input`) > 138')->limit(20)->get();
        $lists->map(function ($val){
            $input = str_replace("0xa9059cbb","",$val->input);
            $receiver = "0x".substr(substr($input,0,64),-40,40);
            $token_value = '0x'.ltrim(substr($input,64,64),'0');
            $val->to = $receiver;
            $val->value = hexdec($token_value);
            $val->save();
        });
    }
}