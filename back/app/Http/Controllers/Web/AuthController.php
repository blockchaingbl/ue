<?php
// +----------------------------------------------------------------------
// | Fanwe 方维系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth.web');
    }
}