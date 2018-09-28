<?php

namespace App\Http\Controllers\Manage;


use App\Http\Models\TransferOrder;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;


class TransController extends AuthBaseController
{

    public function index(Request $request)
    {
        $param = $request->all();
        foreach ($param as $i=>$j)
        {
            $param[$i] = trim($j);
        }
        $lists = TransferOrder::with('from_user','to_user','ex_rate_info')->where(function ($query)use($param){
            if($param['date']){
                $dateStr = explode('~',$param['date']);
                $begin_date = $dateStr[0];
                $end_date = $dateStr[1];
                if($begin_date){
                    $query->where('create_time','>=',"$begin_date 00:00:00");
                }
                if($end_date){
                    $query->where('create_time','<=',"$end_date 23:59:59");
                }
            }
            if($param['from'])
            {
                $from_user = User::where('username',$param['from'])->orWhere('mobile',$param['from'])->value('id');
                $query->where('from',$from_user);
            }
            if($param['to'])
            {
                $to_user = User::where('username',$param['to'])->orWhere('mobile',$param['to'])->value('id');
                $query->where('to',$to_user);
            }
        })->orderBy('id','desc')->paginate(20);
        return view("manage.transaction.index",['lists'=>$lists,'param'=>$param]);
    }




}
