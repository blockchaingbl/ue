<?php
namespace App\Open\Services\App\Util;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Common\SmsCode;
use App\Http\Models\Common\SmsList;
use App\Http\Models\SubscribeToken;
use App\Http\Models\Web\Base;
use App\Open\Services\FanweBaseService;
use Illuminate\Support\Facades\DB;


class InitApi extends FanweBaseService
{

    /**
     * @name index
     * @description  初始化系统，返回全局的一些状态变量和系统配置
     * @param 无
     * @return
     * app_name APP名称
     * app_describe APP描述
     * route_domain 域名
     * coin_uint 虚拟币单位
     * coin_price 当前虚拟币的市值（RMB元），作为OTC市场报价
     * withdraw_rate 提现的手续费比例
     * min_withdraw 最小的提现额度
     * system_notice 系统公告
     * sign_cp 每日签到获取的算力
     * sign_coin 每日签到获取的待返还币
     * invite_cp 每邀请一人获取的算力
     * invite_coin 每邀请一人获取的待返还币
     * app_download_url APP下载地址
     * is_app 是否为APP
     * otc: 交易所是否开启
     * otc_sale_price: OTC是否允许用户出价
     * otc_saleprice_rate：用户出价比例
     * wallet_chain:钱包是否上链
     * friends:是否开启好友功能
     * otc_sale_auth：OTC卖家是否需要授权
     * ...后续陆续更新
     */
    public function index($param)
    {
        $this->setData("app_name",config('app.name'));
        $this->setData("app_describe",config('app.describe'));
        $this->setData("route_domain","http://www.".config('app.route_domain'));
        $this->setData("coin_uint",db_config("COIN_UNIT"));
        $this->setData("coin_price",number_format(db_config("COIN_PRICE"),2));
        $this->setData("withdraw_rate",db_config("WITHDRAW_RATE"));
        $this->setData("min_withdraw",db_config("MIN_WITHDRAW"));
        $this->setData("system_notice",db_config("SYSTEM_NOTICE"));
        $this->setData("min_otc_sale",db_config("MIN_OTC_SALE"));
        $this->setData("min_otc_buy",db_config("MIN_OTC_BUY"));
        $this->setData("sign_cp",db_config("SIGN_CP"));
        $this->setData("sign_coin",db_config("SIGN_COIN"));
        $this->setData("invite_cp",db_config("INVITE_CP"));
        $this->setData("invite_coin",db_config("INVITE_COIN"));
        $this->setData("app_download_url","http://".config("app.route_domain")."/download");
        $this->setData("is_app",Helper::isApp());
        $this->setData("otc",config("app.otc"));
        $this->setData("otc_sale_price",config("app.otc_sale_price"));
        $this->setData("otc_saleprice_rate",db_config("OTC_SALEPRICE_RATE"));
        $this->setData("wallet_chain",config("app.wallet_chain"));
        $this->setData("otc_fee_rate",db_config("OTC_FEE_RATE"));
	    $this->setData("withdraw_open",db_config("WITHDRAW_OPEN"));
        $this->setData("otc_order_overtime",db_config("OTC_ORDER_OVERTIME"));
        $this->setData("token_add",config("app.token_add"));
        $this->setData("friends",config("app.friends"));
        $this->setData('lock_transfer',config('app.lock_transfer'));
        $this->setData('cms',config('app.cms'));
        $this->setData('market',config('app.market'));
        $this->setData('sugar',config('app.sugar'));
        $this->setData('otc_sale_auth',db_config("OTC_SALE_AUTH"));
        $this->setData('about_open',intval(db_config("ABOUT_OPEN")));
        $this->setData('connect_open',intval(db_config("CONNECT_OPEN")));
        $this->setData('notice_open',intval(db_config("NOTICE_TEXT_OPEN")));
        $this->setData('transfer_open',intval(db_config('TRANSFER_OPEN')));
        $this->setData('withdraw_limit_rate',db_config('WITHDRAW_LIMIT_RATE'));
        $this->setData('transfer_over_limit',floatval(db_config('TRANSFER_OVER_LIMIT')));
        return $this->success();
    }

    /**
     * @name incharge_token
     * @description  充值token信息
     * @param
     * id：资产ID
     * @return
     * list
     */
    public function incharge_token($param)
    {
        $id = $param["id"];
        $list = SubscribeToken::leftJoin("coin_type","coin_type.token_id","=","subscribe_token.id")
            ->where(function($query) use($id){
                if($id>0)
                {
                    //查询指定的资产
                    $query->where([
                        "coin_type.id"=>$id,
                        "subscribe_token.isopen"=>1,
                        "coin_type.status"=>1
                    ]);
                }
                else
                {
                    //查询可兑换的资产
                    $query->where([
                        "subscribe_token.isopen"=>1,
                        "coin_type.exchange_open"=>1,
                        "coin_type.status"=>1
                    ])
                    ->orWhere("subscribe_token.platform_coin",1);
                }
            })
            ->select("subscribe_token.*","coin_type.exchange_open")
            ->get();
        $this->setData("list",$list);
        return $this->success();
    }

    /**
     * @name get_mobile_code
     * @description  获取手机国际区号
     * @param
     * id：资产ID
     * @return
     * code_list
     */
    public function get_mobile_code($param){
        $resource = [
            "China-中国-86",
            "Hongkong-香港-852",
            "Taiwan-台湾省-886",
            "United States of America-美国-1",
            "Angola-安哥拉-0244",
            "Afghanistan-阿富汗-93",
            "Albania-阿尔巴尼亚-335",
            "Algeria-阿尔及利亚-213",
            "Andorra-安道尔共和国-376",
            "Anguilla-安圭拉岛-1254",
            "Antigua and Barbuda-安提瓜和巴布达-1268",
            "Argentina-阿根廷-54",
            "Armenia-亚美尼亚-374",
            "Ascension-阿森松-247",
            "Australia-澳大利亚-61",
            "Austria-奥地利-43",
            "Azerbaijan-阿塞拜疆-994",
            "Bahamas-巴哈马-1242",
            "Bahrain-巴林-973",
            "Bangladesh-孟加拉国-880",
            "Barbados-巴巴多斯-1246",
            "Belarus-白俄罗斯-375",
            "Belgium-比利时-32",
            "Belize-伯利兹-501",
            "Benin-贝宁-229",
            "Bermuda Is-百慕大群岛-1441",
            "Bolivia-玻利维亚-591",
            "Botswana-博茨瓦纳-267",
            "Brazil-巴西-55",
            "Brunei-文莱-673",
            "Bulgaria-保加利亚-359",
            "Burkina Faso-布基纳法索-226",
            "Burma-缅甸-95",
            "Burundi-布隆迪-257",
            "Cameroon-喀麦隆-237",
            "Canada-加拿大-1",
            "Cayman Is-开曼群岛-1345",
            "Central African Republic-中非共和国-236",
            "Chad-乍得-235",
            "Chile-智利-56",
            "Colombia-哥伦比亚-57",
            "Congo-刚果-242",
            "Cook Is-库克群岛-682",
            "Costa Rica-哥斯达黎加-506",
            "Cuba-古巴-53",
            "Cyprus-塞浦路斯-357",
            "Czech Republic-捷克-420",
            "Denmark-丹麦-45",
            "Djibouti-吉布提-253",
            "Dominica Rep-多米尼加共和国-1890",
            "Ecuador-厄瓜多尔-593",
            "Egypt-埃及-20",
            "EI Salvador-萨尔瓦多-503",
            "Estonia-爱沙尼亚-372",
            "Ethiopia-埃塞俄比亚-251",
            "Fiji-斐济-679",
            "Finland-芬兰-358",
            "France-法国-33",
            "French Guiana-法属圭亚那-594",
            "French Polynesia-法属玻利尼西亚-689",
            "Gabon-加蓬-241",
            "Gambia-冈比亚-220",
            "Georgia-格鲁吉亚-995",
            "Germany-德国-49",
            "Ghana-加纳-233",
            "Gibraltar-直布罗陀-350",
            "Greece-希腊-30",
            "Grenada-格林纳达-1809",
            "Guam-关岛-1671",
            "Guatemala-危地马拉-502",
            "Guinea-几内亚-224",
            "Guyana-圭亚那-592",
            "Haiti-海地-509",
            "Honduras-洪都拉斯-504",
            "Hungary-匈牙利-36",
            "Iceland-冰岛-354",
            "India-印度-91",
            "Indonesia-印度尼西亚-62",
            "Iran-伊朗-98",
            "Iraq-伊拉克-964",
            "Ireland-爱尔兰-353",
            "Israel-以色列-972",
            "Italy-意大利-39",
            "Ivory Coast-科特迪瓦-225",
            "Jamaica-牙买加-1876",
            "Japan-日本-81",
            "Jordan-约旦-962",
            "Kampuchea (Cambodia )-柬埔寨-855",
            "Kazakstan-哈萨克斯坦-327",
            "Kenya-肯尼亚-254",
            "Korea-韩国-82",
            "Kuwait-科威特-965",
            "Kyrgyzstan-吉尔吉斯坦-331",
            "Laos-老挝-856",
            "Latvia-拉脱维亚-371",
            "Lebanon-黎巴嫩-961",
            "Lesotho-莱索托-266",
            "Liberia-利比里亚-231",
            "Libya-利比亚-218",
            "Liechtenstein-列支敦士登--423",
            "Lithuania-立陶宛-370",
            "Luxembourg-卢森堡-352",
            "Macao-澳门-853",
            "Madagascar-马达加斯加-261",
            "Malawi-马拉维-265",
            "Malaysia-马来西亚-60",
            "Maldives-马尔代夫-960",
            "Mali-马里-223",
            "Malta-马耳他-356",
            "Mariana Is-马里亚那群岛-1670",
            "Martinique-马提尼克-596",
            "Mauritius-毛里求斯-230",
            "Mexico-墨西哥-52",
            "Moldova-摩尔多瓦-373",
            "Monaco-摩纳哥-377",
            "Mongolia-蒙古-976",
            "Montserrat Is-蒙特塞拉特岛-1664",
            "Morocco-摩洛哥-212",
            "Mozambique-莫桑比克-258",
            "Namibia-纳米比亚-264",
            "Nauru-瑙鲁-674",
            "Nepal-尼泊尔-977",
            "Netheriands Antilles-荷属安的列斯-599",
            "Netherlands-荷兰-31",
            "New Zealand-新西兰-64",
            "Nicaragua-尼加拉瓜-505",
            "Niger-尼日尔-227",
            "Nigeria-尼日利亚-234",
            "North Korea-朝鲜-850",
            "Norway-挪威-47",
            "Oman-阿曼-968",
            "Pakistan-巴基斯坦-92",
            "Panama-巴拿马-507",
            "Papua New Cuinea-巴布亚新几内亚-675",
            "Paraguay-巴拉圭-595",
            "Peru-秘鲁-51",
            "Philippines-菲律宾-63",
            "Poland-波兰-48",
            "Portugal-葡萄牙-351",
            "Puerto Rico-波多黎各-1787",
            "Qatar-卡塔尔-974",
            "Reunion-留尼旺-262",
            "Romania-罗马尼亚-40",
            "Russia-俄罗斯-7",
            "Saint Lueia-圣卢西亚-1758",
            "Saint Vincent-圣文森特岛-1784",
            "Samoa Eastern-东萨摩亚(美)-684",
            "Samoa Western-西萨摩亚-685",
            "San Marino-圣马力诺-378",
            "Sao Tome and Principe-圣多美和普林西比-239",
            "Saudi Arabia-沙特阿拉伯-966",
            "Senegal-塞内加尔-221",
            "Seychelles-塞舌尔-248",
            "Sierra Leone-塞拉利昂-232",
            "Singapore-新加坡-65",
            "Slovakia-斯洛伐克-421",
            "Slovenia-斯洛文尼亚-386",
            "Solomon Is-所罗门群岛-677",
            "Somali-索马里-252",
            "South Africa-南非-27",
            "Spain-西班牙-34",
            "SriLanka-斯里兰卡-94",
            "St.Lucia-圣卢西亚-1758",
            "St.Vincent-圣文森特-1784",
            "Sudan-苏丹-249",
            "Suriname-苏里南-597",
            "Swaziland-斯威士兰-268",
            "Sweden-瑞典-46",
            "Switzerland-瑞士-41",
            "Syria-叙利亚-963",
            "Tajikstan-塔吉克斯坦-992",
            "Tanzania-坦桑尼亚-255",
            "Thailand-泰国-66",
            "Togo-多哥-228",
            "Tonga-汤加-676",
            "Trinidad and Tobago-特立尼达和多巴哥-1809",
            "Tunisia-突尼斯-216",
            "Turkey-土耳其-90",
            "Turkmenistan-土库曼斯坦-993",
            "Uganda-乌干达-256",
            "Ukraine-乌克兰-380",
            "United Arab Emirates-阿拉伯联合酋长国-971",
            "United Kiongdom-英国-44",
            "Uruguay-乌拉圭-598",
            "Uzbekistan-乌兹别克斯坦-233",
            "Venezuela-委内瑞拉-58",
            "Vietnam-越南-84",
            "Yemen-也门-967",
            "Yugoslavia-南斯拉夫-381",
            "Zimbabwe-津巴布韦-263",
            "Zaire-扎伊尔-243",
            "Zambia-赞比亚-260"
        ];
        $res = [];
        foreach ($resource as $item) {
            $arr = explode('-',$item);
            $res[]=['name'=>$arr[1],'value'=>'+'.$arr[2]];
        }

        $this->setData('code_list',$res);
        return $this->success('');
    }

}