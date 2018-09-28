<?php

namespace App\Http\Controllers\Web;



use App\Http\Models\CoinType;
use App\Http\Models\ExRate;
use App\Http\Models\PriceLog;
use App\Http\Models\SubscribeToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ExchangeRateController extends BaseController
{

    //
    protected  $financeRateUrl = "https://sapi.k780.com/?app=finance.rate&tcur=CNY";//美元汇率接口网址 2小时更新一次
    protected  $appkey;
    protected  $sign;
    protected  $rate;

    public function __construct()
    {
        set_time_limit(0);
        $lock = Cache::get("rate-fetch");
        if($lock){
            exit('locked');
        }else{
            $lock_time = 120;
            Cache::put("rate-fetch","locked",$lock_time);
        }
        $this->appkey = config('app.nowapi.AppKey');
        $this->sign = config('app.nowapi.Sign');
        $this->financeRateUrl.='&appkey='.$this->appkey;
        $this->financeRateUrl.='&sign='.$this->sign;

    }

    public function fetch()
    {
        DB::beginTransaction();
        try{
            $lists = ExRate::where('open',1)->orderBy('update_time','asc')->limit(5)->get();
            $lists->map(function ($val){
                $url = $this->financeRateUrl."&scur={$val->symbol}";
                $result = $this->curl_get($url);
                $result = json_decode($result);

                if($result->success==1 && $result->result->rate>0)
                {
                    $val->rate = $result->result->rate;
                    $val->update_time = date('Y-m-d H:i:s');
                    $val->save();
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
