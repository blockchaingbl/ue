<?php
namespace App\Open\Services\App\Market;


use App\Http\Models\CoinType;
use App\Http\Models\PriceLog;
use App\Open\Services\FanweAuthService;



class MarketApi extends FanweAuthService
{
    /**
     * @name index
     * @description 价格行情
     * @param
     * page：页数
     * @return market_lists
     * [
     * "id" => 币种id,
     * "usd_price" => 美元价格,
     * "price" => 人民币价格,
     * "rate_abs" => 涨跌幅度,
     * "trend" => 0 跌 1 涨,
     * "coin_unit" => 单位,
     * "name" =>名称，
     * 'open' 开盘价格
     * 'high' 最高
     * 'low'  最低
     *
     * ]
     */
    public function index($param)
    {
        $coin_type = CoinType::select('id','price_usd','price','coin_unit','name','api_param')->where('api_available','=',1)->paginate(10)->toArray()['data'];
        $coin_type = collect($coin_type)->map(function($val){
             $last_day_price = PriceLog::where('api_timestamp','>',strtotime('-1 day'))
             ->where('api_param',$val['api_param'])
             ->orderBy('api_timestamp','asc')->first();
             $val['rate_abs'] = 1-($last_day_price->last / $val['price_usd']);
             $val['trend'] = 0;
             $now_price = PriceLog::select('open','high','low','volume')->where('api_param',$val['api_param'])
             ->orderBy('id','desc')->first();
             $val['open'] = $now_price->open;
             $val['high'] = $now_price->high;
             $val['low'] = $now_price->low;
             $val['volume'] = $now_price->volume;
             $val['price'] = number_format($val['price'],2,'.','');
             if($val['rate_abs'] >=0)
                 $val['trend'] = 1;
             $val['rate_abs'] = number_format(abs($val['rate_abs']*100),2,'.','');
            return $val;
        })->toArray();
        $this->setData('market_lists',$coin_type);
        return $this->success();
    }

    /**
     * @name detail
     * @description 行情详情
     * @param
     * api_param
     * divide 区分间隔 'min' 'hour2' 'hour4' 'day'
     *
     * @return [
     *  'date' 时间数组
     *  'price' 价格数组,
     * ]

     */
    public function detail($param)
    {
        $api_param = $param['api_param'];
        $divide = $param['divide'];
        $date = [];
        $price = [];
        $data = [];

        switch ($divide)
        {
            case 'min' :
                $lists = PriceLog::where('api_timestamp','>',strtotime('-5 hour'))
                    ->where('api_param','=',$api_param)
                    ->groupBy('create_min')->orderBy('api_timestamp','asc')->limit(60)->get()->toArray();
                foreach ($lists as $item)
                {
                    $date[]=date('H:i',$item['api_timestamp']);
                }
                break;
            case 'hour2':
                $lists = PriceLog::where('api_timestamp','>',strtotime('-1 week'))
                ->where('api_param','=',$api_param)
                ->groupBy('create_hour')->orderBy('api_timestamp','asc')->limit(60)->get()->toArray();
                foreach ($lists as $item){
                    $date[]= date('H:i',$item['api_timestamp']);
                }
                break;
            case 'hour4':
                $lists = PriceLog::where('api_timestamp','>',strtotime('-2 week'))
                ->where('api_param','=',$api_param)
                ->groupBy('create_hour')->orderBy('api_timestamp','asc')->limit(120)->get()->toArray();
                foreach ($lists as $item){
                    $price[]=$item['last'];
                    $date[]= date('H:i',$item['api_timestamp']);
                }
                break;
            case 'day' :
                $lists = PriceLog::where('api_timestamp','>',strtotime('-2 month'))
                ->where('api_param','=',$api_param)
                ->orderBy('api_timestamp','asc')
                ->groupBy('create_day')->limit(60)->get()->toArray();
                foreach ($lists as $item){
                    $date[]= date('Y-m-d',$item['api_timestamp']);
                }
                break;
            default :
                return $this->error('参数错误');
                break;
        }
        if($lists){
            foreach ($lists as $item){
                $price[]= floatval($item['last']);
            }
            $this->setData('date',$date);
            $this->setData('price',$price);
            return $this->success();
        }else{
            return $this->error('暂无数据');
        }


    }
}