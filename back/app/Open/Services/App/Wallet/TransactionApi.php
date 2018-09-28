<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\EthereumTransaction;
use App\Http\Models\SubscribeToken;
use App\Open\Services\FanweEthereumService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class TransactionApi extends FanweEthereumService
{

    /**
     * @name txlistdb
     * @description 数据库本地的交易记录
     * @param $param
     * @return array
     */
    public function txlistdb($param)
    {
        $address = $param['address'];
        $page = intval($param['page']);
        $coin = $param['coin'];
        $token = SubscribeToken::where("token_name",$coin)->first();
        if($page==0)$page=1;
        if($token->token_type==1)
            $result = $this->getTransactionEthListFromDb($address,$token->token_name,$page);
        else
            $result = $this->getTransactionTokenListFromDb($address,$coin,$page);
        if($result['errcode'])
        {
            return $this->error($result['message'],$result['errcode']);
        }
        else
        {
            $tx_list = $result['data']['result'];
            $this->setData("tx_list",$tx_list);
            $this->setData("page",$page);

            $config_chains = config("app.block_chain");
            $chain = $config_chains[$token->block_chain];
            $this->setData("base_coin",$chain['base_coin']);
            $this->setData("ethereum_confirm_count",$chain['ethereum_confirm_count']);

            return $this->success();
        }
    }


    /**
     * @name confirm
     * @description 交易确认
     * @param $param     *
     * @return array
     */
    public function confirm($param){
        $tx_hashes = $param['tx_hashes'];
        $confirmed_list = EthereumTransaction::whereIn("hash",$tx_hashes)->whereRaw("confirmed = 1")->get();
        $tx_list = EthereumTransaction::whereIn("hash",$tx_hashes)->get();
        $this->setData("confirmed_list",$confirmed_list);
        $this->setData("tx_list",$tx_list);
        return $this->success();
    }


    /**
     * @name getdata
     * @description 获取合约交易的data
     * @param $param
     * contract: 合约名称，对应的abi的别名,标准代币为token20，其余的根据项目
     * func: 合约方法的名称
     * params: 参数表列json
     * @return
     * txdata:返回的数据
     */
    public function getdata($param){

        $contract = $param['contract'];
        $func = $param['func'];
        $params = $param['params'];
        $params = json_decode($params,true);
        $params = is_array($params)?$params:[];
        $data =$this->contract_tx_data($func,$params,$contract);
        $this->setData("txdata",$data);
        return $this->success();
    }


    /**
     * @name estimate
     * @description 获取交易的数量，用于签名下一笔交易的nonce
     * @param $param
     * from:付款人
     * to:收款人
     * value:金额eth
     * data:额外信息
     * @return
     * nonce 表示下一笔交易签名用的nonce
     * gas 预估的gas
     */
    public function estimate($param){


        $coin = $param['coin'];
        $token = SubscribeToken::where("token_name",$coin)->first();
        $config_chains = config("app.block_chain");
        $chain = $config_chains[$token->block_chain];
        $this->setData("base_coin",$chain['base_coin']);
        $this->ethereum->change_chain($token->block_chain);
        $this->setData("block_chain",$token->block_chain);

        $from = $param['from'];
        $nonce = $this->ethereum->eth_getTransactionCount($from,"latest",true);
        if($nonce===null)
            return $this->error("交易出错");

        $value = $param['value'];
        $data = "0x".str_replace("0x","",$param['data']);
        if($token->token_type==1)
        {
            //以太币
            $to = $param['to'];
        }
        else
        {
            if(config("app.token_add"))
            {
                $token = SubscribeToken::where(["token_name"=>$coin])->first();
                $to = $token->contract_address;
            }
            else {
                $tokens = config("app.tokens");
                $token = $tokens[$coin];
                $to = $token['address'];  //合约地址
            }
            $reciever = $param['to'];
            $data =$this->contract_tx_data("transfer",[$reciever,$value]);
            $value = "0x0";
        }

        //预估gas
        $message = new \Ethereum_Message($from,$to,"0x0","0x0",$value,$data,$nonce);
        $gas = $this->ethereum->eth_estimateGas($message,"latest");
        $gas = hexdec($gas);
        if($gas===null)
            return $this->error("交易出错");


        $this->setData("data",$data);
        $this->setData("gas",$gas);
        $this->setData("nonce",$nonce);
        $this->setData("transfer_value",$value);
        $this->setData("transfer_address",$to);

        return $this->success();
    }



    /**
     * @name sendraw
     * @description 将签名过的交易发送到以太坊
     * @param $param raw:签名
     * @return tx  交易单
     */
    public function sendraw($param){
        $chain = $param['block_chain'];
        $this->ethereum->change_chain($chain);

        $raw = $param['raw'];
        try{
            $res = $this->ethereum->request("eth_sendRawTransaction",[$raw]);
            $tx_hash = $res->result;
            if(!$tx_hash)
            {
                return $this->error("交易出错".$res->error->message);
            }
            $tx_data = $this->ethereum->eth_getTransactionByHash($tx_hash);
            if($tx_data){
                //开始入库
                Helper::saveTransactionTx($tx_data,$chain);
            }
            $this->setData("tx_hash",$tx_hash);
            return $this->success();
        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }


    /**
     * @name estimate_manage
     * @description 获取交易的数量，用于签名下一笔交易的nonce,本估算用于后台转账的估算，主要是token配置不同
     * @param $param
     * from:付款人
     * to:收款人
     * value:金额eth
     * contract_address:代币地址 0x表示eth
     * data:额外信息
     * @return
     * nonce 表示下一笔交易签名用的nonce
     * gas 预估的gas
     */
    public function estimate_manage($param){
        $chain = $param['block_chain'];
        $this->ethereum->change_chain($chain);

        $from = $param['from'];
        $nonce = intval($param['nonce']);
        $value = $param['value'];
        $contract_address = $param['contract_address'];
        $data = "0x".str_replace("0x","",$param['data']);
        if($contract_address=="0x")
        {
            //以太币
            $to = $param['to'];
        }
        else
        {
            $reciever = $param['to'];
            $to = $contract_address;  //合约地址
            $data =$this->contract_tx_data("transfer",[$reciever,$value]);
            $value = "0x0";
        }
        //预估gas
        $message = new \Ethereum_Message($from,$to,"0x0","0x0",$value,$data,$nonce);
        $gas = $this->ethereum->eth_estimateGas($message,"latest");
        $gas = hexdec($gas);
        if($gas===null||$gas==0)
            return $this->error("交易估算出错");
        $this->setData("data",$data);
        $this->setData("gas",$gas);
        $this->setData("nonce",$nonce);
        $this->setData("transfer_value",$value);
        $this->setData("transfer_address",$to);
        $this->setData("block_chain",$chain);
        return $this->success();
    }


}