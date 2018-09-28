<?php
namespace App\Open\Services\App\Util;

use App\Http\Models\PriceTrend;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\DB;

class ChartApi extends FanweBaseService
{

    /**
     * @name pricetrend
     * @description 价格走势
     * @param 无
     * @return
     * date 日期数据集
     * price 价格数据集
     * max 最高
     * min 最低
     */
    public function pricetrend($param)
    {
        $day = 10; //获取近10天
        $data = [];
        $date = [];
        $price = [];
        $last_price = number_format(db_config("COIN_PRICE"),2);

        //根据日期，取当天最新的价格
        $list = DB::select("select * from (select DATE_FORMAT(create_time,'%Y-%m-%d') as date,price from fanwe_price_trend order by id desc) a group by a.date order by a.date desc limit ".$day);
        $list = json_decode(json_encode($list),true);

        for($i=($day-1);$i>=0;$i--)
        {
            $day_date = date("Y-m-d",strtotime("-".$i." day"));
            if($last_price==0)
                $last_price = PriceTrend::where("create_time","<",$day_date." 00:00:00")->max("price");
            foreach($list as $value)
            {
                if(in_array($day_date,$value))
                {
                    $data[$day_date] = $value["price"];
                    $last_price = $value["price"];
                    break;
                }
                else
                {
                    $data[$day_date] = $last_price;
                }
            }
            $date[] = date("m.d",strtotime("-".$i." day"));
        }

        foreach($data as $value)
        {
            $price[] = $value;
        }

        $max = max($price);
        $min = min($price);
        $this->setData("date",$date);
        $this->setData("price",$price);
        $this->setData("max",$max);
        $this->setData("min",$min);
        return $this->success();
    }

}