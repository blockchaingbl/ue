<?php

namespace App\Open\Services;



use App\FanweErrcode;
use App\Http\Models\Web\User;
use App\Open\Services\App\User\AccountApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class FanweAuthService extends FanweBaseService
{

    protected $user;
    //初始化平台
    public function init($param)
    {
        $user = $this->init_user($param);
        if($user['errcode'])
        {
            return $user;
        }
        $this->user = $user;
        return parent::init($param);
    }

	
}
