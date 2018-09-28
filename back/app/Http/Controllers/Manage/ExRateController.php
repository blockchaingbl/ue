<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\Adversts;
use App\Http\Models\AdverstsCategory;
use App\Http\Models\Article;
use App\Http\Models\CoinType;
use App\Http\Models\ExRate;
use App\Http\Models\Manage\Admin;
use App\Http\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExRateController extends AuthBaseController
{

    //广告管理
    public function index(Request $request)
    {
        $open = $request->input('open','all');
        $keyword = $request->input('keyword');
        $lists = ExRate::where(function ($query)use ($open,$keyword){
            if($open!=='all')
            {
                $query->where('open',$open);
            }
            if($keyword)
            {
                $query->where('name','like',"%{$keyword}%");
                $query->orWhere('symbol','like',"%{$keyword}%");
            }
        })->orderBy('open','desc')->orderBy('sort','asc')->paginate(20);
        return view("manage.transaction.exrate",['lists'=>$lists,'keyword'=>$keyword,'open'=>$open]);
    }

    public function change(Request $request)
    {
        $id = $request->input('id');
        $open = $request->input('open');
        $sort = $request->input('sort');
        $name = $request->input('name');
        $rate= $request->input('rate');
        $exRate = ExRate::find($id);
        $exRate->open = $open;
        $exRate->sort = $sort;
        $exRate->name = $name;
        $exRate->rate = $rate;
        $exRate->save();
        return $this->success();
    }


}
