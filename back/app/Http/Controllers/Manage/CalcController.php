<?php

namespace App\Http\Controllers\Manage;


use App\Http\Models\Apply;
use App\Http\Models\ApplyNode;
use App\Http\Models\ApplyTeacher;
use App\Http\Models\CalcLog;
use App\Http\Models\CourseOrder;
use App\Http\Models\LockTransferAuth;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;
use App\Http\Models\CourseAuth;
use Illuminate\Support\Facades\DB;


class CalcController extends AuthBaseController
{



    public function index($id)
    {
       $calc_log = CalcLog::where('user_id',$id)->orderBy('id','desc')->first();

       $lists = User::whereIn('id',json_decode($calc_log->children))->paginate(20);

       return view('manage.calc.index',['lists'=>$lists,'calc_log'=>$calc_log]);
    }




}
