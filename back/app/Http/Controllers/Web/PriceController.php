<?php

namespace App\Http\Controllers\Web;



use App\Http\Models\CoinType;
use App\Http\Models\PriceLog;
use App\Http\Models\SubscribeToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class PriceController extends BaseController
{

    //
    protected  $fetchUrl = 'https://www.bitstamp.net/api/v2/ticker/';// 虚拟币价格接口 10min 最多600次
    protected  $financeRateUrl = "https://sapi.k780.com/?app=finance.rate&scur=USD&tcur=CNY";//美元汇率接口网址 2小时更新一次
    protected  $appkey;
    protected  $sign;
    protected  $rate;

    public function __construct()
    {
        set_time_limit(0);
        $lock = Cache::get("price-fetch");
        if($lock){
            exit('locked');
        }else{
            $lock_time = intval(db_config('PRICE_FETCH_LOCK_TIME'));
            Cache::put("price-fetch","locked",$lock_time);
        }

        if(Cache::has('finance_rate')){
            $this->rate = floatval(Cache::get('finance_rate'));
        }

        if(!$this->rate){
            $this->appkey = config('app.nowapi.AppKey');
            $this->sign = config('app.nowapi.Sign');
            $this->financeRateUrl.='&appkey='.$this->appkey;
            $this->financeRateUrl.='&sign='.$this->sign;
            $result = $this->curl_get($this->financeRateUrl);
            $result = json_decode($result);
            if($result->success==1){
                $this->rate = $result->result->rate;
            }else{
                Log::warn('finance rate api error');
            }

            if(!$this->rate)
                $this->rate = config("app.usdtocny");
            Cache::put('finance_rate',$this->rate,120);
        }
    }

    public function fetch()
    {
        DB::beginTransaction();
        try{
            $lists = CoinType::where('api_available','1')->get();
            $lists->map(function ($list){
                $url = $this->fetchUrl.$list->api_param;
                $result = $this->curl_get($url);
                $result = json_decode($result);
                $price_log = new PriceLog();
                $price_log->create_time = date('Y-m-d H:i:s');
                $price_log->create_hour = date('Y-m-d H',$result->timestamp);
                $price_log->create_day = date('Y-m-d',$result->timestamp);
                $price_log->create_min = date('Y-m-d H:i',$result->timestamp);
                $price_log->last = $result->last;
                $price_log->high = $result->high;
                $price_log->low = $result->low;
                $price_log->vwap = $result->vwap;
                $price_log->volume = $result->volume;
                $price_log->bid = $result->bid;
                $price_log->ask = $result->ask;
                $price_log->api_param = $list->api_param;
                $price_log->api_timestamp = $result->timestamp;
                $price_log->open = $result->open;
                $price_log->usd_rate = $this->rate;
                $price_log->save();
                $list->price = $price_log->last * $this->rate;
                $list->update_time = date('Y-m-d H:i:s');
                $list->price_usd = $price_log->last;
                $list->save();
                $subscribeToken = SubscribeToken::where("token_symbol",$list->coin_unit)->first();
                if($subscribeToken)
                {
                    $subscribeToken->incharge_rate = $list->price;
                    $subscribeToken->save();
                }
            });
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            Log::warn($e->getMessage());
            return "Error";
        }
    }
    public function curl_get($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}
