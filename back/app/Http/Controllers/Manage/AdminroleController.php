<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\Adversts;
use App\Http\Models\AdverstsCategory;
use App\Http\Models\Article;
use App\Http\Models\CoinType;
use App\Http\Models\Connect;
use App\Http\Models\Manage\Admin;
use App\Http\Models\Manage\AdminRole;
use App\Http\Models\Manage\AdminRoleAccess;
use App\Http\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdminroleController extends AuthBaseController
{

    public function index(Request $request)
    {
        $lists = AdminRole::all();
        foreach($lists as $k=>$row){
            $lists[$k]->access_group = AdminRoleAccess::where("role_id",$row->id)->get()->toArray();
        }
        $access_list = config("manage.manage_access.access_list");
        foreach($access_list as $k=>$v)
        {
            if(!config("app.otc"))
            {
                //otc关闭
                if($k=="group_transaction"||$k=="group_octauth"){
                    unset($access_list[$k]);
                }
            }
            if(!config('app.cms')){
                if($k=="group_cms"){
                    unset($access_list[$k]);
                }
            }
        }
        return view("manage.adminrole.index",['lists'=>$lists,"access_list"=>$access_list]);
    }


    public function insertrole(Request $request){
        $admin_role = new AdminRole();
        $admin_role->role_name = $request->input("role_name");
        if(!$admin_role->role_name){
            return $this->error("请填写角色名称");
        }
        $admin_role->save();
        foreach($request->input("access_group") as $item){
            $admin_role_access = new AdminRoleAccess();
            $admin_role_access->role_id = $admin_role->id;
            $admin_role_access->access_group = $item;
            $admin_role_access->save();
        }
        return $this->success("保存成功");
    }

    public function updaterole(Request $request){
        $admin_role = AdminRole::where("id",$request->input("id"))->first();
        if(!$admin_role)
            return $this->error("角色数据不存在");
        $admin_role->role_name = $request->input("role_name");
        if(!$admin_role->role_name){
            return $this->error("请填写角色名称");
        }
        $admin_role->save();
        AdminRoleAccess::where("role_id",$admin_role->id)->delete();
        foreach($request->input("access_group") as $item){
            $admin_role_access = new AdminRoleAccess();
            $admin_role_access->role_id = $admin_role->id;
            $admin_role_access->access_group = $item;
            $admin_role_access->save();
        }
        return $this->success("保存成功");
    }

    public function delrole(Request $request){
        $admin_role = AdminRole::where("id",$request->input("id"))->first();
        if(!$admin_role)
            return $this->error("角色数据不存在");

        $admin_role->delete();
        AdminRoleAccess::where("role_id",$admin_role->id)->delete();
    }

    public function connect(Request $request)
    {
        $param = $request->all();

        $lists=  Connect::with('user')->where(function($query)use($param){
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
        })->orderBy('id','desc')->paginate(20);
        return view("manage.setting.connect",['lists'=>$lists,'param'=>$param]);

    }

    public function huifu(Request $request)
    {
        try {
            $id = $request->input('id');
            $connect= Connect::find($id);
            if(!$connect)
            {
                return $this->error('id不存在');
            }
            $connect->huifu = $request->input('huifu');
            $connect->save();
            return $this->success();
        }catch (\Exception $e) {
           return $this->error($e->getMessage());
        }

    }

}
