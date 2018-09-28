<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function __construct(){

    }


    public function wx_avatar()
    {
        $image = file_get_contents(realpath("shuhang/images/default.jpg"));
        header('Content-type: image/jpg');
        echo $image;
    }


}
