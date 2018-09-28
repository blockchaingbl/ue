<?php
namespace App\Open\Services\App\Wallet;

use App\FanweErrcode;
use App\Helper;
use App\Open\Services\FanweEthereumService;


class InitApi extends FanweEthereumService
{

    /**
     * @name index
     * @description  初始化一些全局变量与配置
     * @param
     * @return
     */
    public function index($param)
    {
        $config = [
            "RecieveUrlBase" => "http://www.".config("app.route_domain")."/#/wallet/send/"
        ];
        $this->setData("config",$config);
        return $this->success();
    }

}