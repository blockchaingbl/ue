<?php
/**
 * Simple JSON-RPC interface.
 */
 
class JSON_RPC
{
	protected $host, $port, $version,$url,$net,$chain;
	protected $id = 0;
	
	function __construct($host, $port, $version="2.0")
	{
		$this->host = $host;
		$this->port = $port;
		$this->version = $version;

        $config_chains = config("app.block_chain");
        $block_chain = "platform";
        $chain = $config_chains[$block_chain];
        $this->url = $chain['ethereum_api_domain']."/api?module=proxy&action=";
        $this->net = $chain['net_type'];
        $this->chain = $chain;
	}

    function change_chain($block_chain){
        $config_chains = config("app.block_chain");
        if(!$config_chains[$block_chain])$block_chain = "platform";
        $chain = $config_chains[$block_chain];

        $this->host = $chain['rpc_server_host'];
        $this->port = $chain['rpc_server_port'];
        $this->url = $chain['ethereum_api_domain']."/api?module=proxy&action=";
        $this->net = $chain['net_type'];
        $this->chain = $chain;
    }

    function request($method, $params=array()){
	    if($this->net=="rpc")
	        return $this->rpc_request($method,$params);
	    else
	        return $this->api_request($method,$params);
    }

    function api_request($method, $params=array())
    {
        $method = str_replace("eth_","",$method);
        if(method_exists($this,$method))
            $url = $this->$method($params);
        else{
            throw new RPCException($method." not supported!");
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//		curl_setopt($ch, CURLOPT_POST, TRUE);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        $ret = curl_exec($ch);
        if($ret !== FALSE)
        {
            $formatted = $this->format_response($ret);

            if(isset($formatted->error))
            {
                \Illuminate\Support\Facades\Log::warn($url." ".print_r($formatted,true));
                throw new RPCException("交易发送失败，可能原因：<br/><div style='text-align:left;margin-top:5px;'>1. ETH不足以支付算力Gas<br/>2. 算力Gas费用太低，请增加算力<br/>3. 有交易未完成，请等待或者增加算力替换原交易</div>",$formatted->error->code);
            }
            else
            {
                return $formatted;
            }
        }
        else
        {
            \Illuminate\Support\Facades\Log::warn($url." Server did not respond");
            throw new RPCException("区块链网络没有响应");
        }
    }
    //以下是api_request用到的同名的rpc服务的url组装
    private function blockNumber($params){
        $url = $this->url."eth_".__FUNCTION__;
        return $url;
    }
    private function getBalance($params){

        $url = $this->chain['ethereum_api_domain']."/api?module=account&action=balance&address=".$params[0]."&tag=latest";
        return $url;
    }
    private function getTransactionCount($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&address=".$params[0]."&tag=".$params[1];
        return $url;

    }
    private function getTransactionByBlockNumberAndIndex($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&tag=".$params[0]."&index=".$params[1];
        return $url;
    }
    private function getBlockByNumber($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&tag=".$params[0]."&boolean=".$params[1];
        return $url;
    }

    private function call($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&to=".$params[0]['to'];
        $url.="&data=".$params[0]['data'];
        $url.="&tag=latest";
        return $url;
    }
    private function estimateGas($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&from=".$params[0]['from'];
        $url.="&to=".$params[0]['to'];
        $url.="&data=".$params[0]['data'];
        $url.="&value=".$params[0]['value'];
        $url.="&gas=0x0&gasPrice=0x0";
        return $url;
    }
    private function getTransactionByHash($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&txhash=".$params[0];
        return $url;
    }
    private function getTransactionReceipt($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&txhash=".$params[0];
        return $url;
    }
    private function sendRawTransaction($params){
        $url = $this->url."eth_".__FUNCTION__;
        $url.="&hex=".$params[0];
        return $url;
    }
	
	function rpc_request($method, $params=array())
	{
		$data = array();
		$data['jsonrpc'] = $this->version;
		$data['id'] = $this->id++;
		$data['method'] = $method;
		$data['params'] = $params;
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $this->host);
		curl_setopt($ch, CURLOPT_PORT, $this->port);

        if($this->port=="443")
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
        }

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        $ret = curl_exec($ch);
		if($ret !== FALSE)
		{
			$formatted = $this->format_response($ret);
			
			if(isset($formatted->error))
			{
				//throw new RPCException($formatted->error->message, $formatted->error->code);

                \Illuminate\Support\Facades\Log::warn($formatted->error);
                throw new RPCException("交易发送失败，可能原因：<br/><div style='text-align:left;margin-top:5px;'>1. ETH不足以支付算力Gas<br/>2. 算力Gas费用太低，请增加算力<br/>3. 有交易未完成，请等待或者增加算力替换原交易</div>",$formatted->error->code);

            }
			else
			{
				return $formatted;
			}
		}
		else
		{
            \Illuminate\Support\Facades\Log::warn("Server did not respond");
            throw new RPCException("区块链网络没有响应");
		}
	}
	
	function format_response($response)
	{
		return @json_decode($response);
	}
}

class RPCException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() 
    {
        return __CLASS__ . ": ".(($this->code > 0)?"[{$this->code}]:":"")." {$this->message}\n";
    }
}