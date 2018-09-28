<?php
namespace App\Open\Services\App\Demo;

use App\FanweErrcode;
use App\Open\Services\FanweBaseService;


class DemoApi extends FanweBaseService
{

    public function run($param)
    {
        $this->setData("param",$param);
        return $this->success();
    }

}