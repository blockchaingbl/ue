<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\EthereumAccount;
use App\Http\Models\SubscribeToken;
use App\Http\Models\UserSubscribeToken;
use App\Http\Models\UserWallet;
use App\Open\Services\FanweEthereumService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class PropertydbApi extends FanweEthereumService
{

    /**
     * @name sum
     * @description  查询指定地址下的虚拟币资产情况
     * @param
     * address:地址
     * @return
     * [
     *  {
            "name":"代币名称",
     *      "font":"iconfont显示用的字体class"
     *      "amount":"余额"
     *      "notify":"通知数"
     *  }
     * ]
     */
    public function sum($param)
    {
        $address = $param['address'];
        $result = $this->custom_erc_token($address);
        return $result;
    }


    /**
     * @name custom_erc_token
     * @description  查询指定地址下的代币资产情况（从用户自定义上传表获取erc代币）
     * @param
     * address:地址
     * @return
     */
    public function custom_erc_token($address)
    {
        try {
            $properties = [];
            //默认显示的币种
            $tokens = SubscribeToken::where(["default"=>1,"isopen"=>1])->whereIn("token_type",[1,2])->orderBy("id")->get();
            foreach ($tokens as $token) {
                if($token->token_type==1)
                {
                    try{
                        //以太币
                        $ethereum_account = EthereumAccount::where(["address"=>$address,"contract_address"=>"0x","block_chain"=>$token->block_chain])->first();
                        if(!$ethereum_account)
                        {
                            $this->ethereum->change_chain($token->block_chain);

                            $ethereum_account = new EthereumAccount();
                            $ethereum_account->address = $address;
                            $ethereum_account->contract_address = "0x";
                            $ethereum_account->block_number = $this->ethereum->eth_blockNumber(true);
                            $ethereum_account->value = $this->getEtherBalanceWei($address);
                            $ethereum_account->block_chain = $token->block_chain;
                            $ethereum_account->save();
                        }

                    }catch (\Exception $e){
                        Log::warn($e->getMessage());
                        $ethereum_account = EthereumAccount::where(["address"=>$address,"contract_address"=>"0x","block_chain"=>$token->block_chain])->first();
                    }
                    $amount = $ethereum_account->value / pow(10,18);
                }
                elseif ($token->token_type==2)
                {
                    try{
                        //代币
                        $token_account = EthereumAccount::where(["address"=>$address,"contract_address"=>$token->contract_address,"block_chain"=>$token->block_chain])->first();
                        if(!$token_account)
                        {
                            $this->ethereum->change_chain($token->block_chain);

                            $token_account = new EthereumAccount();
                            $token_account->address = $address;
                            $token_account->contract_address = $token->contract_address;
                            $token_account->block_number = $this->ethereum->eth_blockNumber(true);
                            $token_account->value = $this->getErcBlanceWei($token->contract_address,$address);
                            $token_account->block_chain = $token->block_chain;
                            $token_account->save();
                        }
                    }catch (\Exception $e)
                    {
                        $token_account = EthereumAccount::where(["address"=>$address,"contract_address"=>$token->contract_address,"block_chain"=>$token->block_chain])->first();
                    }
                    $amount = $token_account->value / pow(10,$token->token_decimals);
                }
                else
                {
                    $amount = 0;
                }

                $block_chains = config("app.block_chain");
                $chain = $block_chains[$token->block_chain];
                $token_item = [
                    "name" => $token->token_symbol,
                    "type" => $token->token_name,
                    "font" => $token->token_name,
                    "icon" => $token->icon?$token->icon:asset("coin_icon/eth.png"),
                    "amount" => $amount,
                    "price" => $token->incharge_rate,
                    "notify" => "0",
                    "decimals" => $token->token_decimals,
                    'default_gas_price' => $chain['default_gas_price']
                ];
                array_push($properties, $token_item);
            }
            //用户订阅的代币(开启的)
            $user_id = UserWallet::where("address",$address)->value("user_id");
            $user_tokens = UserSubscribeToken::leftJoin("subscribe_token","subscribe_token.id","=","user_subscribe_token.token_id")
                ->where([
                    "user_subscribe_token.user_id"=>$user_id,
                    "user_subscribe_token.status"=>1,
                    "subscribe_token.isopen"=>1
                ])->whereIn("subscribe_token.token_type",[1,2])->get();
            foreach ($user_tokens as $token) {

                try{
                    //代币
                    $token_account = EthereumAccount::where(["address"=>$address,"contract_address"=>$token->contract_address,"block_chain"=>$token->block_chain])->first();
                    if(!$token_account)
                    {
                        $this->ethereum->change_chain($token->block_chain);

                        $token_account = new EthereumAccount();
                        $token_account->address = $address;
                        $token_account->contract_address = $token->contract_address;
                        $token_account->block_number = $this->ethereum->eth_blockNumber(true);
                        $token_account->value = $this->getErcBlanceWei($token->contract_address,$address);
                        $token_account->block_chain = $token->block_chain;
                        $token_account->save();
                    }
                }catch (\Exception $e){
                    $token_account = EthereumAccount::where(["address"=>$address,"contract_address"=>$token->contract_address])->first();
                }
                $amount = $token_account->value / pow(10,$token->token_decimals);
//                $amount = $this->getErcBlance($token['contract_address'], $address);
                $block_chains = config("app.block_chain");
                $chain = $block_chains[$token->block_chain];
                $token_item = [
                    "name" => $token->token_symbol,
                    "type" => $token->token_name,
                    "font" => $token->token_name,
                    "icon" => $token->icon?$token->icon:asset("coin_icon/eth.png"),
                    "amount" => $amount,
                    "price" => $token->incharge_rate,
                    "notify" => "0",
                    "decimals" => $token->token_decimals,
                    'default_gas_price' => $chain['default_gas_price']
                ];
                array_push($properties, $token_item);
            }
            $properties = array_unique($properties, SORT_REGULAR);
            $properties_set = [];
            foreach($properties as $k=>$v)
            {
                $properties_set[] = $v;
            }
            $this->setData("properties", $properties_set);
            return $this->success();
        }catch(\Exception $e){
            return $this->error("error:".$e->getMessage());
        }
    }

}