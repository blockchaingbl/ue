<?php

namespace App\Open\Services;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\EthereumTokenTransaction;
use App\Http\Models\EthereumTransaction;
use App\Http\Models\SubscribeToken;
use Illuminate\Support\Facades\Config;

include_once(base_path("app/Library/Ethereum/ethereum.php"));
class FanweEthereumService extends FanweBaseService
{

    protected $ethereum;
    //初始化平台
    public function init($param)
    {
        $config_chains = config("app.block_chain");
        $block_chain = "platform";
        $chain = $config_chains[$block_chain];

        $this->ethereum = new \Ethereum($chain['rpc_server_host'], $chain['rpc_server_port']);
        return parent::init($param);
    }

    protected function getEtherBalance($address)
    {
        $amount = $this->ethereum->eth_getBalance($address,"latest",true);
        $amount = round($amount/(pow(10,18)),5);
        return $amount;
    }
    protected function getEtherBalanceWei($address)
    {
        $amount = $this->ethereum->eth_getBalance($address,"latest",true);
        return $amount;
    }

    //查询ERC代币的余额
    protected function getErcBlance($erc_address,$address)
    {
        $amount = $this->contract_call($erc_address,"balanceOf",[$address]);
        if(strpos($amount[0],"0x")!==false)
        {
            $amount = hexdec($amount[0]);
        }
        else
        {
            $amount = $amount[0];
        }
        $decimals = $this->contract_call($erc_address,"decimals");
        $decimals = intval($decimals[0]);
        if($decimals>0)
            $decimals=pow(10,$decimals);
        else
            $decimals = 1;
        $amount = round($amount/$decimals,5);
        return $amount;
    }
    protected function getErcBlanceWei($erc_address,$address)
    {
        $amount = $this->contract_call($erc_address,"balanceOf",[$address]);
        if(strpos($amount[0],"0x")!==false)
        {
            $amount = hexdec($amount[0]);
        }
        else
        {
            $amount = $amount[0];
        }
        return $amount;
    }

    //生成指定合约执行的data
    protected function contract_tx_data($func,$param,$contract_type="token20"){
        $param_data[] = Helper::getContractFunctionHash($func,$contract_type);
        $dynamic_data = []; //动态变量
        $position_padding = 0; //表示动态变量的偏移量
        foreach($param as $k=>$v)
        {
            if(substr($v,0,2)=="0x")
            {
                $hex = str_replace("0x","",$v);
            }
            elseif(is_numeric($v)) //只支持整型，不超过32位
            {
                $hex = dechex(intval($v)); //将每个的数转成16进制
            }
            elseif(is_bool($v))
            {
                $hex = $v?"1":"0";
            }
            elseif(is_string($v)) //目前只支持 英文的 string
            {
                $length_hex = dechex(strlen($v));
                $length_hex = str_pad($length_hex,64,"0",STR_PAD_LEFT);
                $unicode_v = Helper::unicode_encode($v);
                $unicode_v = str_replace("\\u0","",$unicode_v);
                $content_hex = str_pad($unicode_v,64,"0",STR_PAD_RIGHT);
                $dynamic_data[] = $length_hex.$content_hex;

                $hex =  dechex(intval($position_padding + count($param))*32); //动态变量的位置
                $position_padding+=2; //字符串只间隔两个
            }
            elseif(is_array($v)) //只支持整型数组
            {
                $length_hex = dechex(count($v)); //数组长度
                $length_hex = str_pad($length_hex,64,"0",STR_PAD_LEFT);
                $content_hex = "";
                foreach($v as $arr_item)
                {
                    $hex_item = dechex(intval($arr_item));
                    $hex_item = str_pad($hex_item,64,"0",STR_PAD_LEFT);
                    $content_hex.=$hex_item;
                }
                $dynamic_data[] = $length_hex.$content_hex;
                $hex =  dechex(intval($position_padding + count($param))*32); //动态变量的位置
                $position_padding+=(count($v)+1); //数组间隔长度+1，未测试
            }
            $hex = str_pad($hex,64,"0",STR_PAD_LEFT);
            $param_data[] = $hex;
        }
        foreach($dynamic_data as $item)
        {
            $param_data[] = $item;
        }
        $data = implode("",$param_data);  //eth_call的data
        return $data;
    }
    //请求合约的view与pure方法。eth_call,参数只允许整型与16进制字段串。
    protected function contract_call($contract_address,$func,$param,$contract_type="token20"){
        $data = $this->contract_tx_data($func,$param,$contract_type);

        $res = $this->ethereum->request("eth_call",[["to"=>$contract_address,"data"=>$data],"latest"]);
        $data_fetched = $res->result;
        $data = str_replace("0x","",$data_fetched);
        $param_count = strlen($data)/64;
        if($param_count==0&&$data_fetched)
        {
            $param_count = 1;
        }
        $params = [];
        for($i=0;$i<$param_count;$i++)
        {
            $param_item = substr($data,$i*64,64);
            $param_item = ltrim($param_item,"0");
            if(strlen($param_item)<=8)
            {
                //32位位数够，转成整型
                $param_item = hexdec("0x".$param_item);
            }
            else
            {
                $param_item = "0x".str_pad($param_item,64,"0",STR_PAD_LEFT);
            }
            $params[] = $param_item;
        }
        return $params;
    }

    protected function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $rs = curl_exec($curl);
        $err = curl_error($curl);
        $err_no = curl_errno($curl);
        curl_close($curl);
        if($err)
        {
            return ['errcode'=>$err_no,"message"=>$err,"data"=>''];
        }
        else
            return ['errcode'=>0,"message"=>"","data"=>$rs];
    }

    /**
     * 生成SAAS系统API调用的请求参数，请求参数中将包含基本的参数（如：platform_id,timestamp,signature），并返回包含这些参数的数组数据。
     *
     * @param $args 具体的接口调用参数（除platform_id, timestamp, signature外），PHP数组对象，如：array("domain"=>"xxx.yydb.fanwe.com")
     * @return 已生成的参数数组，如：array("platform_id"=>"","timestamp"=>1457839056,"signature"=>"")
     */
    private function makeRequestParameters($args)
    {
        $instance = Helper::load_api_key("push");
        // 计算参数签名，并设置返回值
        $systime = time();
        $params = array();
        $result = array();
        foreach($args as $key=>$value) {
            if ($key == 'platform_id' || $key == 'timestamp' || $key == 'signature') continue;
            $params[] = $key.$value;
            $result[$key] = $value;
        }
        $params['platform_id'] = 'platform_id'.$instance->platform_id;
        $params['timestamp'] = 'timestamp'.$systime;
        sort($params, SORT_STRING);
        $paramsStr = implode($params);
        $signature = md5($instance->platform_secret.$paramsStr.$instance->platform_secret);
        $result['platform_id'] = $instance->platform_id;
        $result['timestamp'] = $systime;
        $result['signature'] = $signature;


        // 返回结果
        return $result;
    }
    protected function httpEncPost($url,$param){

        $params = $this->makeRequestParameters($param);
        // 执行HTTP POST请求
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); // 服务地址
        curl_setopt($ch, CURLOPT_HEADER, false); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // POST请求方式
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $rs = curl_exec($ch);
        $err = curl_error($ch);
        $err_no = curl_errno($ch);
        curl_close($ch);
        if($err)
        {
            return ['errcode'=>$err_no,"message"=>$err,"data"=>''];
        }
        else{
            if(strtolower(trim($rs))=="success")
                return ['errcode'=>0,"message"=>"","data"=>$rs];
            else
                return ['errcode'=>FanweErrcode::SYSTEM_ERROR,"message"=>"","data"=>$rs];
        }

    }

    //查询以太坊的交易记录
    protected function getTransactionEthListFromDb($address,$coin,$page){

        $token = SubscribeToken::where(["token_name"=>$coin])->first();
        $decimals = $token->token_decimals;

        $page_size = 30;
        $page = $page-1<0?0:$page-1;
        $tx_list = EthereumTransaction::where(function($query) use ($address){
                $query->where("ethereum_transaction.from",$address)
                ->orWhere("ethereum_transaction.to",$address);
            })
            ->where("block_chain",$token->block_chain)
            ->select("ethereum_transaction.*")
            ->offset($page*$page_size)->limit($page_size)->orderBy("ethereum_transaction.time_stamp","desc")->get();
        $result['data']['result']  = $tx_list->toArray();

        foreach( $result['data']['result'] as $k=>$v)
        {
            $item['blockNumber'] = $v['block_number'];
            $item['timeStamp'] = $v['time_stamp'];
            $item['hash'] = $v['hash'];
            $item['nonce'] = $v['nonce'];
            $item['blockHash'] = $v['block_hash'];
            $item['transactionIndex'] = $v['transaction_index'];
            $item['from'] = $v['from'];
            $item['to'] = $v['to'];
            $item['value'] = $v['value'];
            $item['gas'] = $v['gas'];
            $item['gasPrice'] = $v['gas_price'];
            $item['isError'] = $v['is_error'];
            $item['txreceipt_status'] = $v['txreceipt_status'];
            $item['input'] = $v['input'];
            $item['contract_address'] = $v['contract_address'];
            $item['cumulativeGasUsed'] = $v['cumulative_gas_used'];
            $item['gasUsed'] = $v['gas_used'];
            $item['confirmations'] = $v['confirmations'];
            $item['pending'] = $v['pending'];
            $item['is_error'] = $v['is_error'];


            $item['value'] = round($item['value']/pow(10,$decimals),5);
            $item['datetime'] = date("Y-m-d H:i:s",$item['timeStamp']);
            if($item['from']==$address)
            {
                //支出
                $item['txType'] = "zhichu";
                if($item['value']>0)
                    $item['formatValue'] = "-".$item['value'];
                else
                    $item['formatValue'] = 0;
                if(!$item['to'])
                    $item['hashShow'] =  $item['contractAddress'];
                else
                    $item['hashShow'] = $v['to'];

            }
            else
            {
                //收入
                $item['txType'] = "shouru";
                if($item['value']>0)
                    $item['formatValue'] = "+".$item['value'];
                else
                    $item['formatValue'] = 0;
                $item['hashShow'] = $item['from'];
            }
            $item['unit'] = $token->token_symbol;
            $item['gasFee'] = round(($item['gas']*$item['gasPrice'])/pow(10,$decimals),10);

            $result['data']['result'][$k] = $item;
        }

        return $result;
    }
//    protected function getTransactionEthList($address,$page){
//        $page_size = 30;
//        $url = config("app.ethereum_api_domain")."/api?module=account&action=txlist&address=".$address."&startblock=0&endblock=latest&page=".$page."&offset=".$page_size."&sort=desc";
//        $result = $this->httpGet($url);
//        $result['data'] = json_decode($result['data'],true);
//        foreach($result['data']['result'] as $k=>$v)
//        {
//            $result['data']['result'][$k]['value'] = round($v['value']/pow(10,18),5);
//            $result['data']['result'][$k]['datetime'] = date("Y-m-d H:i:s",$v['timeStamp']);
//            if($v['from']==$address)
//            {
//                //支出
//                $result['data']['result'][$k]['txType'] = "zhichu";
//                if($result['data']['result'][$k]['value']>0)
//                    $result['data']['result'][$k]['formatValue'] = "-".$result['data']['result'][$k]['value'];
//                else
//                    $result['data']['result'][$k]['formatValue'] = 0;
//                if(!$v['to'])
//                    $result['data']['result'][$k]['hashShow'] = $v['contractAddress'];
//                else
//                    $result['data']['result'][$k]['hashShow'] = $v['to'];
//
//            }
//            else
//            {
//                //收入
//                $result['data']['result'][$k]['txType'] = "shouru";
//                if($result['data']['result'][$k]['value']>0)
//                    $result['data']['result'][$k]['formatValue'] = "+".$result['data']['result'][$k]['value'];
//                else
//                    $result['data']['result'][$k]['formatValue'] = 0;
//                $result['data']['result'][$k]['hashShow'] = $v['from'];
//            }
//            $result['data']['result'][$k]['unit'] = "eth";
//            $result['data']['result'][$k]['gasFee'] = round(($v['gas']*$v['gasPrice'])/pow(10,18),10);
//        }
//        return $result;
//    }



    protected function getTransactionTokenListFromDb($address,$coin,$page){
        $token = SubscribeToken::where(["token_name"=>$coin])->first();
        $token_address = $token->contract_address;
        $decimals = $token->token_decimals;

        $page_size = 30;
        $page = $page-1<0?0:$page-1;
        //$tx_list = EthereumTokenTransaction::whereRaw("(`from`='".$address."' or `to`='".$address."') and contract_address = '".$token_address."' and block_chain = '.$token->block_chain.'")->offset($page*$page_size)->limit($page_size)->orderBy("time_stamp","desc")->get();

        $tx_list = EthereumTokenTransaction::where(function($query) use ($address){
                $query->where("ethereum_token_transaction.from",$address)
                    ->orWhere("ethereum_token_transaction.to",$address);
            })
            ->where(["block_chain"=>$token->block_chain,"contract_address"=>$token_address])
            ->select("ethereum_token_transaction.*")
            ->offset($page*$page_size)->limit($page_size)->orderBy("ethereum_token_transaction.time_stamp","desc")->get();
        $result['data']['result']  = $tx_list->toArray();


        foreach($result['data']['result'] as $k=>$v)
        {

            $item['blockNumber'] = $v['block_number'];
            $item['timeStamp'] = $v['time_stamp'];
            $item['hash'] = $v['hash'];
            $item['nonce'] = $v['nonce'];
            $item['blockHash'] = $v['block_hash'];
            $item['transactionIndex'] = $v['transaction_index'];
            $item['from'] = $v['from'];
            $item['to'] = $v['to'];
            $item['value'] = $v['value'];
            $item['gas'] = $v['gas'];
            $item['gasPrice'] = $v['gas_price'];
            $item['input'] = $v['input'];
            $item['contract_address'] = $v['contract_address'];
            $item['cumulativeGasUsed'] = $v['cumulative_gas_used'];
            $item['gasUsed'] = $v['gas_used'];
            $item['confirmations'] = $v['confirmations'];
            $item['tokenName'] = $v['token_name'];
            $item['tokenSymbol'] = $v['token_symbol'];
            $item['pending'] = $v['pending'];
            $item['is_error'] = $v['is_error'];

            $item['value'] = round($item['value']/pow(10,$decimals),5);
            $item['datetime'] = date("Y-m-d H:i:s",$item['timeStamp']);
            if($item['from']==$address)
            {
                //支出
                $item['txType'] = "zhichu";
                if($item['value']>0)
                    $item['formatValue'] = "-".$item['value'];
                else
                    $item['formatValue'] = 0;

                $item['hashShow'] = $v['to'];

            }
            else
            {
                //收入
                $item['txType'] = "shouru";
                if($item['value']>0)
                    $item['formatValue'] = "+".$item['value'];
                else
                    $item['formatValue'] = 0;

                $item['hashShow'] = $item['from'];
            }
            $item['unit'] = $item['tokenSymbol'];
            $item['gasFee'] = round(($item['gas']*$item['gasPrice'])/pow(10,18),10);

            $result['data']['result'][$k] = $item;
        }
        return $result;
    }

//    protected function getTransactionTokenList($address,$coin,$page){
//        if(config("app.token_add"))
//        {
//            $token = SubscribeToken::where(["token_name"=>$coin])->first();
//            $token_address = $token->contract_address;
//        }
//        else {
//            $tokens = config("app.tokens");
//            $token = $tokens[$coin];
//            $token_address = $token['address'];
//        }
//
//        $page_size = 30;
//        $url = config("app.ethereum_api_domain")."/api?module=account&action=tokentx&contractaddress=".$token_address."&address=".$address."&page=".$page."&offset=".$page_size."&sort=desc";
//        $result = $this->httpGet($url);
//        $result['data'] = json_decode($result['data'],true);
//        foreach($result['data']['result'] as $k=>$v)
//        {
//            $result['data']['result'][$k]['value'] = round($v['value']/pow(10,18),5);
//            $result['data']['result'][$k]['datetime'] = date("Y-m-d H:i:s",$v['timeStamp']);
//            if($v['from']==$address)
//            {
//                //支出
//                $result['data']['result'][$k]['txType'] = "zhichu";
//                if($result['data']['result'][$k]['value']>0)
//                    $result['data']['result'][$k]['formatValue'] = "-".$result['data']['result'][$k]['value'];
//                else
//                    $result['data']['result'][$k]['formatValue'] = 0;
//
//                $result['data']['result'][$k]['hashShow'] = $v['to'];
//
//            }
//            else
//            {
//                //收入
//                $result['data']['result'][$k]['txType'] = "shouru";
//                if($result['data']['result'][$k]['value']>0)
//                    $result['data']['result'][$k]['formatValue'] = "+".$result['data']['result'][$k]['value'];
//                else
//                    $result['data']['result'][$k]['formatValue'] = 0;
//
//                $result['data']['result'][$k]['hashShow'] = $v['from'];
//            }
//            $result['data']['result'][$k]['unit'] = $v['tokenSymbol'];
//            $result['data']['result'][$k]['gasFee'] = round(($v['gas']*$v['gasPrice'])/pow(10,18),10);
//        }
//        return $result;
//    }

    //获取并解析交易单
    protected function getTransactionReceiptLogs($tx_hash){
        $transaction = $this->ethereum->eth_getTransactionReceipt($tx_hash);
        return $transaction->logs;
    }

    //查询ERC代币的信息
    protected function getErcContract($erc_address)
    {
        $symbol = $this->contract_call($erc_address,"symbol",[]);
        $symbol = $this->decodeErcName($symbol);
        $name = $this->contract_call($erc_address,"name",[]);
        $name = $this->decodeErcName($name);
        $decimals = $this->contract_call($erc_address,"decimals");
        $decimals = intval($decimals[0]);
        $res['symbol'] = $symbol;
        $res['name'] = $name;
        $res['decimals'] = $decimals;
        return $res;
    }

    //解析代币名称
    protected function decodeErcName($res)
    {
        //解析名字
        $name_code = str_replace("0x","",$res[2]);
        $name_len = $res[1];
        $name_unicode = "";
        for($i=0;$i<$name_len;$i++)
        {
            $name_unicode.='\u00'.substr($name_code,$i*2,2);
        }
        $res = Helper::unicode_decode($name_unicode);
        return $res;
    }

	
}
