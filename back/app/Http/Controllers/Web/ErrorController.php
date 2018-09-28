<?php
namespace App\Http\Controllers\Web;


use App\Helper;
use App\Http\Models\Pony;
use App\Library\CurlInvoker;
use Illuminate\Support\Facades\View;

class ErrorController extends BaseController
{

    public function index()
    {
        return view("web.404");
    }

}
