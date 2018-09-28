<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\SubscribeToken;
use App\Http\Models\UserSubscribeToken;
use App\Http\Models\UserWallet;
use App\Open\Services\FanweEthereumService;
use Illuminate\Support\Facades\Cache;


class PropertyApi extends FanweEthereumService
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
        if(config("app.token_add"))
        {
            $result = $this->custom_erc_token($address);
        }
        else
        {
            $result = $this->config_erc_token($address);
        }
        return $result;
    }


    /**
     * @name config_erc_token
     * @description  查询指定地址下的代币资产情况（从config配置获取erc代币）
     * @param
     * address:地址
     * @return
     */
    public function config_erc_token($address)
    {
        try {
            //查询以太币
            $ethereum_amount = $this->getEtherBalance($address);
            $properties = [
                [
                    "name" => "ETH",
                    "type" => "ethereum",
                    "font" => "ethereum",
                    "icon" => asset("coin_icon/eth.png"),
                    "amount" => $ethereum_amount,
                    "notify" => "0",
                    "decimals" => 18
                ]
            ];

            //ERC代币查询
            $tokens = config("app.tokens");
            foreach ($tokens as $token_type => $token) {
                $amount = $this->getErcBlance($token['address'], $address);
                $token_item = [
                    "name" => $token['name'],
                    "type" => $token_type,
                    "font" => $token_type,
                    "icon" => asset("coin_icon/" . strtolower($token['name']) . ".png"),
                    "amount" => $amount,
                    "notify" => "0",
                    "decimals" => $token["decimals"]
                ];
                array_push($properties, $token_item);
            }
            $this->setData("properties", $properties);
            return $this->success();
        }catch(\Exception $e){
            return $this->error("error:".$e->getMessage());
        }
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
                    //以太币
                    $amount = $this->getEtherBalance($address);
                }
                elseif ($token->token_type==2)
                {
                    //代币
                    $amount = $this->getErcBlance($token['contract_address'], $address);
                }
                else
                {
                    $amount = 0;
                }
                $token_item = [
                    "name" => $token->token_symbol,
                    "type" => $token->token_name,
                    "font" => $token->token_name,
                    "icon" => $token->icon?$token->icon:asset("coin_icon/eth.png"),
                    "amount" => $amount,
                    "price" => $token->incharge_rate,
                    "notify" => "0",
                    "decimals" => $token->token_decimals
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
                $amount = $this->getErcBlance($token['contract_address'], $address);
                $token_item = [
                    "name" => $token->token_symbol,
                    "type" => $token->token_name,
                    "font" => $token->token_name,
                    "icon" => $token->icon?$token->icon:asset("coin_icon/eth.png"),
                    "amount" => $amount,
                    "price" => $token->incharge_rate,
                    "notify" => "0",
                    "decimals" => $token->token_decimals
                ];
                array_push($properties, $token_item);
            }
            $properties = array_unique($properties, SORT_REGULAR);
            $this->setData("properties", $properties);
            return $this->success();
        }catch(\Exception $e){
            return $this->error("error:".$e->getMessage());
        }
    }

}