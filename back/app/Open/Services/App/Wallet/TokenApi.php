<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\SubscribeToken;
use App\Http\Models\UserSubscribeToken;
use App\Http\Models\UserWallet;
use App\Open\Services\FanweAuthService;
use App\Open\Services\FanweEthereumService;
use Illuminate\Support\Facades\Cache;


class TokenApi extends FanweEthereumService
{

    /**
     * @name index
     * @description 代币列表
     * @param
     * page：页数
     * keyword：token名称或合约地址
     * address：账户地址
     * @return
     */
    public function index($param)
    {
        $keyword = trim($param["keyword"]);
        $address = $param["address"];
        $user_id = UserWallet::where("address",$address)->value("user_id");

        //非默认，平台人工添加的代币
        $platform_tokens = SubscribeToken::leftJoin("user_subscribe_token",function($join) use($user_id){
            $join->on("user_subscribe_token.token_id","=","subscribe_token.id")
                ->where("user_subscribe_token.user_id","=",$user_id);
            })
            ->select("subscribe_token.*","user_subscribe_token.user_id","user_subscribe_token.status")
            ->where([
                "subscribe_token.default"=>0,
                "subscribe_token.type"=>1,
                "subscribe_token.isopen"=>1
            ])
            ->where(function($query) use($keyword){
                if($keyword)
                {
                    $query->where("subscribe_token.token_symbol","like","%".$keyword."%");
                }
            })
            ->get()->toArray();

        //用户自己添加的代币
        $user_tokens = UserSubscribeToken::leftJoin("subscribe_token","subscribe_token.id","=","user_subscribe_token.token_id")
            ->select("subscribe_token.*","user_subscribe_token.user_id","user_subscribe_token.status")
            ->where([
                "user_subscribe_token.user_id"=>$user_id,
                "subscribe_token.default"=>0,
                "subscribe_token.type"=>0,
                "subscribe_token.isopen"=>1
            ])
            ->where(function($query) use($keyword){
                if($keyword)
                {
                    $query->where("subscribe_token.token_symbol","like","%".$keyword."%");
                }
            })
            ->get()->toArray();
        $tokens_data = array_merge($platform_tokens,$user_tokens);

        //格式化
        $tokens = [];
        foreach($tokens_data as $item)
        {
            $data['id'] = $item['id'];
            $data['contract_address'] = $item['contract_address'];
            $data['token_name'] = $item['token_name'];
            $data['token_symbol'] = $item['token_symbol'];
            $data['icon'] = $item['icon']?$item['icon']:asset("coin_icon/eth.png");
            $data['user_id'] = $item['user_id'];
            $data['status'] = $item['status'];
            $tokens[] = $data;
        }

        $this->setData("tokens",$tokens);
        return $this->success();
    }

    /**
     * @name search
     * @description 代币查询
     * @param
     * page：页数
     * keyword：token名称或合约地址
     * address：账户地址
     * @return
     */
    public function search($param)
    {
        $block_chains = config("app.block_chain");
        $keyword = trim($param["keyword"]);
        $address = $param["address"];
        $user_id = UserWallet::where("address",$address)->value("user_id");
        if(strlen($keyword)==42)
        {
            //订阅表查询不到代币，再去以太坊查询
            $tokens_data = SubscribeToken::leftJoin("user_subscribe_token",function($join) use($user_id){
                $join->on("user_subscribe_token.token_id","=","subscribe_token.id")
                    ->where("user_subscribe_token.user_id","=",$user_id);
                })
                ->select("subscribe_token.*","user_subscribe_token.user_id","user_subscribe_token.status")
                ->where([
                    "subscribe_token.default"=>0,
                    "subscribe_token.type"=>0
                ])
                ->where("subscribe_token.contract_address",$keyword)->get()->toArray();
            if(!$tokens_data)
            {
                foreach($block_chains as $key=>$val){
                    $this->ethereum->change_chain($key);
                    $token_info = $this->getErcContract($keyword);
                    if($token_info['symbol']&&$token_info['name'])
                    {
                        $tokens = new SubscribeToken();
                        $tokens->contract_address = $keyword;
                        $token_symbol = SubscribeToken::where("token_symbol","like","%".$token_info['symbol']."%")->count();
                        if($token_symbol==0)
                            $token_symbol = '';
                        $tokens->token_symbol = $token_info['symbol'].$token_symbol;
                        $token_name = SubscribeToken::where("token_name","like","%".$token_info['name']."%")->count();
                        if($token_name==0)
                            $token_name = '';
                        $tokens->token_name = $token_info['name'].$token_name;
                        $tokens->token_decimals = $token_info['decimals'];
                        $tokens->syn_count = 0;
                        $tokens->token_type = 2;
                        $tokens->block_chain = $key;
                        $tokens->save();
                        $tokens_data = [$tokens];
                    }
                }

            }
            else
            {
                //关闭状态不显示
                if(!$tokens_data[0]['isopen'])
                {
                    $tokens_data = [];
                }
            }
        }
        else
        {
            $tokens_data = SubscribeToken::leftJoin("user_subscribe_token",function($join) use($user_id){
                $join->on("user_subscribe_token.token_id","=","subscribe_token.id")
                    ->where("user_subscribe_token.user_id","=",$user_id);
                })
                ->select("subscribe_token.*","user_subscribe_token.user_id","user_subscribe_token.status")
                ->where([
                    "subscribe_token.default"=>0,
                    "subscribe_token.type"=>0,
                    "subscribe_token.isopen"=>1
                ])
                ->where("subscribe_token.token_symbol","like","%".$keyword."%")->get()->toArray();
        }

        //格式化
        $tokens = [];
        foreach($tokens_data as $item)
        {
            $data['id'] = $item['id'];
            $data['contract_address'] = $item['contract_address'];
            $data['token_name'] = $item['token_name'];
            $data['token_symbol'] = $item['token_symbol'];
            $data['icon'] = $item['icon']?$item['icon']:asset("coin_icon/eth.png");
            $data['user_id'] = $item['user_id'];
            $data['status'] = (!is_null($item['status']))?1:0;
            $tokens[] = $data;
        }

        $this->setData("tokens",$tokens);
        return $this->success();
    }

    /**
     * @name subscribe
     * @description 代币订阅
     * @param
     * token_id: 代币ID
     * address：账户地址
     * @return
     * status：当前状态
     */
    public function subscribe($param)
    {
        $token_id = $param["token_id"];
        $address = $param["address"];
        $user_id = UserWallet::where("address",$address)->value("user_id");
        $user_subscribe = UserSubscribeToken::where(["user_id"=>$user_id,"token_id"=>$token_id])->first();
        if(!$user_subscribe)
        {
            $user_subscribe = new UserSubscribeToken();
            $user_subscribe->user_id = $user_id;
            $user_subscribe->token_id = $token_id;
            $user_subscribe->status = 1;
        }
        else
        {
            $user_subscribe->status = $user_subscribe->status?0:1;
        }
        $res = $user_subscribe->save();
        if($res)
        {
            $this->setData("status",$user_subscribe->status);
            return $this->success();
        }
        else
        {
            return $this->error();
        }
    }

    /**
     * @name gettoken
     * @description 前往以太坊查询代币
     * @param
     * address：合约地址
     * @return
     * status：当前状态
     */
    public function gettoken($param)
    {
        $block_chain = $param['block_chain'];

        $this->ethereum->change_chain($block_chain);
        $contract_address = trim($param["contract_address"]);
        $token_info = $this->getErcContract($contract_address);

        $this->setData("token_info",$token_info);
        return $this->success();
    }


}