<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\CoinType;
use App\Http\Models\Manage\Admin;
use App\Http\Models\Manage\AdminRole;
use App\Http\Models\MineIncharge;
use App\Http\Models\MineLog;
use App\Http\Models\MinePool;
use App\Http\Models\PriceLog;
use App\Http\Models\PriceTrend;
use App\Http\Models\SubscribeToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class SettingController extends AuthBaseController
{

    //平台币
    public function basecoin(Request $request)
    {
        $chart_date_time = $request->input('chart_date_time');
        if($chart_date_time){
            $DateStr = explode('~',$chart_date_time);
            $begin_date = $DateStr[0];
            $end_date = $DateStr[1];
            $param['chart_date_time'] = $begin_date.'~'.$end_date;
        }
        $conf = DB::table('conf')->lists('keyvalue','keyname');
        $chartInfo =  PriceTrend::select(
            DB::raw("DATE_FORMAT(create_time,'%Y-%m-%d') as date,price"))
            ->where(function($query) use($begin_date,$end_date){
                if($begin_date)
                {
                    $query->where("create_time",">",$begin_date." 00:00:00");
                }
                if($end_date)
                {
                    $query->where("create_time","<",$end_date." 23:59:59");
                }
            })
            ->groupBy("date")
            ->orderBy("date","desc")
            ->limit(7)
            ->get()->toArray();
        krsort($chartInfo);
        $platform_token = SubscribeToken::where("platform_coin",1)->first();
        return view("manage.setting.basecoin",['conf'=>$conf,'chartInfo'=>$chartInfo,'param'=>$param,'platform_token'=>$platform_token]);
    }

    //虚拟币
    public function morecoin(Request $request)
    {
        $list = CoinType::leftJoin("subscribe_token","subscribe_token.id","=","coin_type.token_id")
            ->select(
                "coin_type.*",
                "subscribe_token.contract_address",
                "subscribe_token.incharge_address",
                "subscribe_token.token_decimals",
                "subscribe_token.default",
                "subscribe_token.isopen",
                "subscribe_token.token_type",
                "subscribe_token.block_chain"
            )
//            ->where("subscribe_token.type",1)
            ->paginate(10);
        $count = CoinType::count();
        $api =  config('app.support_currency');
        $lock_time = db_config('PRICE_FETCH_LOCK_TIME');
        return view("manage.setting.morecoin",['list'=>$list,'count'=>$count,'lock_time'=>$lock_time,'api'=>$api]);
    }

    //保存平台币配置
    public function save_basecoin_conf(Request $request){
        DB::beginTransaction();
        try{
            //更新平台币配置
            collect($request->all())->map(function ($keyvalue,$keyname){
                if($keyname=='COIN_PRICE'){
                    DB::table('price_trend')->insert([
                        'price'=>$keyvalue,
                        'create_time'=>date('Y-m-d H:i:s'),
                        'type'=>1
                    ]);
                }
                DB::table('conf')->where('keyname',$keyname)->update(['keyvalue'=>$keyvalue]);
            });
            DB::table('conf')->where('keyname','INIT_VERSION')->increment('keyvalue');
            Cache::forget('DB_CONFIG');

            //更新代币信息
            $platform_token = SubscribeToken::where("platform_coin",1)->first();
            if(!$platform_token)
                $platform_token = new SubscribeToken();
            $platform_token->contract_address = trim($request->input('contract_address'));
            $platform_token->token_symbol = $request->input('COIN_UNIT');
            $platform_token->token_name = $request->input('COIN_ENNAME');
            $platform_token->token_decimals = $request->input('COIN_DECIMALS');
            $platform_token->icon = $request->input('COIN_ICON');
            $platform_token->cn_name = $request->input('COIN_CNNAME');
            $platform_token->syn_count = 1;
            $platform_token->incharge_address = trim($request->input('incharge_address'));
            $platform_token->incharge_rate = $request->input('COIN_PRICE');
            $platform_token->default = $request->input('default');
            $platform_token->isopen = $request->input('isopen');
            $platform_token->type = 1;
            $platform_token->token_type = $request->input('token_type');
            $platform_token->block_chain = $request->input('block_chain');
            $platform_token->platform_coin = 1;
            $platform_token->save();

            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    //保存虚拟币配置
    public function save_morecoin_conf(Request $request){
        $block_chain = $request->input('block_chain');
        $contract_address= strtolower(trim($request->input("contract_address")));
        if(!$request->input("name"))
        {
            return $this->error("请填写中文名称");
        }
        if(!$request->input("en_name"))
        {
            return $this->error("请填写英文名称");
        }
        if(!$request->input("coin_unit"))
        {
            return $this->error("请填写单位");
        }
        if(!$request->input('token_decimals'))
        {
            return $this->error("请填写代币精度");
        }
        if(!$request->input("id"))
        {
            //新增时判断
            $check_token = CoinType::where("en_name",$request->input("en_name"))->orWhere("coin_unit",$request->input("coin_unit"))->first();
            if($check_token)
            {
                return $this->error("该资产已经存在");
            }
        }
        if($request->input("withdraw_open")&&!$request->input("withdraw_rate"))
        {
            return $this->error("请填写提现手续费");
        }
        if($request->input("withdraw_open")&&!$request->input("min_withdraw"))
        {
            return $this->error("请填写最小提现额度");
        }
        if($request->input("isopen")==1)
        {
            if($request->input("token_type")==2&&!$request->input('contract_address'))
            {
                return $this->error("请填写合约地址");
            }
            if(!$request->input("id"))
            {
                $check_contract = SubscribeToken::where(["contract_address"=>$contract_address,"block_chain"=>$block_chain])->first();
                if($request->input("token_type")==2&&$check_contract)
                {
                    return $this->error("合约地址已存在");
                }
            }
            if($request->input("incharge_open")&&!$request->input("incharge_address"))
            {
                return $this->error("请填写充币地址");
            }
            if($request->input("exchange_open")&&!$request->input('api_available')&&!$request->input("price"))
            {
                return $this->error("请填写行情价格");
            }
        }
        DB::beginTransaction();
        try{
            //插入资产信息
            $coin_type = CoinType::find($request->input("id"));
            if(!$coin_type)
            {
                $coin_type = new CoinType();
            }
            $coin_type->icon = $request->input("icon");
            $coin_type->name = $request->input("name");
            $coin_type->en_name = $request->input("en_name");
            $coin_type->coin_unit = $request->input("coin_unit");
            $coin_type->description = $request->input("description");
            $coin_type->withdraw_rate = $request->input("withdraw_rate");
            $coin_type->min_withdraw = $request->input("min_withdraw");
            $coin_type->mine_percent = $request->input("mine_percent");
            $coin_type->mine_peroid = $request->input("mine_peroid");
            $coin_type->mine_div = $request->input("mine_div");
            $coin_type->price_unit = $request->input('price_unit','CNY');
            $coin_type->mine_min = $request->input("mine_min");
            $coin_type->limit_rate = $request->input('limit_rate');
            $coin_type->transfer_over_limit = $request->input('transfer_over_limit');
            if(config("app.friends"))
            {
                $coin_type->steal_percent = $request->input("steal_percent");
            }
            $coin_type->api_available = $request->input('api_available');
            $coin_type->price = $request->input('price');
            $coin_type->withdraw_open = $request->input('withdraw_open');
            if($coin_type->api_available){
                $coin_type->api_param = $request->input('api_param');
            }
            $coin_type->incharge_open = $request->input('incharge_open');
            $coin_type->exchange_open = $request->input('exchange_open');
            $coin_type->status = $request->input('status');
            $coin_type->save();

            //插入虚拟币信息
            if(!$coin_type->token_id)
            {
                $subscribeToken = SubscribeToken::where(["contract_address"=>$contract_address,"block_chain"=>$block_chain])->first();
                if(!$subscribeToken)
                {
                    $subscribeToken = new SubscribeToken();
                    $subscribeToken->token_symbol = $coin_type->coin_unit;
                    $subscribeToken->token_name = $coin_type->en_name;
                    $subscribeToken->icon = $coin_type->icon;
                    $subscribeToken->cn_name = $coin_type->name;
                    $subscribeToken->token_decimals = $request->input('token_decimals');
                    $subscribeToken->syn_count = 0;
                    $subscribeToken->incharge_rate = $coin_type->price;
                    $subscribeToken->incharge_address = trim($request->input('incharge_address'));
                    $subscribeToken->default = $request->input('default',0);
                    $subscribeToken->type = 1;
                    $subscribeToken->isopen = $request->input('isopen');
                    $subscribeToken->token_type = $request->input('token_type');
                    if($subscribeToken->token_type==2)
                        $subscribeToken->contract_address = $contract_address;

                    $subscribeToken->block_chain = $block_chain;

                    $subscribeToken->save();
                }
                else
                {
                    $subscribeToken->token_symbol = $coin_type->coin_unit;
                    $subscribeToken->token_name = $coin_type->en_name;
                    $subscribeToken->icon = $coin_type->icon;
                    $subscribeToken->cn_name = $coin_type->name;
                    $subscribeToken->token_decimals = $request->input('token_decimals');
                    $subscribeToken->incharge_rate = $coin_type->price;
                    $subscribeToken->incharge_address = trim($request->input('incharge_address'));
                    $subscribeToken->default = $request->input('default');
                    $subscribeToken->isopen = $request->input('isopen');
                    $subscribeToken->token_type = $request->input('token_type');
                    if($subscribeToken->token_type==2)
                        $subscribeToken->contract_address = $contract_address;

                    $subscribeToken->block_chain = $block_chain;

                    $subscribeToken->save();
                }

                //更新资产的关联ID
                $coin_type->token_id = $subscribeToken->id;
                $coin_type->save();
            }
            else
            {
                $subscribeToken = SubscribeToken::where("id",$coin_type->token_id)->first();
                if(!$subscribeToken)
                {
                    $subscribeToken = new SubscribeToken();
                }
                $subscribeToken->token_symbol = $coin_type->coin_unit;
                $subscribeToken->token_name = $coin_type->en_name;
                $subscribeToken->icon = $coin_type->icon;
                $subscribeToken->cn_name = $coin_type->name;
                $subscribeToken->token_decimals = $request->input('token_decimals');
                $subscribeToken->incharge_rate = $coin_type->price;
                $subscribeToken->incharge_address = trim($request->input('incharge_address'));
                $subscribeToken->default = $request->input('default',0);
                $subscribeToken->isopen = $request->input('isopen');
                $subscribeToken->token_type = $request->input('token_type');
                if($subscribeToken->isopen==1&&$subscribeToken->token_type==2)
                {
                    $subscribeToken->contract_address = $contract_address;
                }
                else
                {
                    $subscribeToken->contract_address = null;
                }
                $subscribeToken->block_chain = $block_chain;
                $subscribeToken->save();
                $coin_type->token_id = $subscribeToken->id;
                $coin_type->save();
            }
            Cache:: forget('COIN_CONFIG');

            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error("操作失败，请稍后重试",FanweErrcode::SYSTEM_ERROR,$e->getMessage());
        }
    }

    //保存公告设置
    public function save_notice(Request $request){
        DB::beginTransaction();
        try{
            collect($request->all())->map(function ($keyvalue,$keyname){
                DB::table('conf')->where('keyname',$keyname)->update(['keyvalue'=>$keyvalue]);
            });
            DB::table('conf')->where('keyname','INIT_VERSION')->increment('keyvalue');
            Cache::forget('DB_CONFIG');
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error();
        }
    }

    //ERC20代币合约验证
    public function check_contract_address(Request $request){
        try{
            $contract_address = $request->input('contract_address');
            $block_chain = $request->input('block_chain');
            $res = Helper::invoke("app.wallet/token/gettoken",["contract_address"=>$contract_address,"block_chain"=>$block_chain]);
            $token_info = $res['data']['token_info'];
            if(!$token_info['symbol'])
            {
                return $this->error("合约地址无效");
            }
            else
            {
                return $this->success("合约地址正确",$token_info);
            }
        }catch(\Exception $e)
        {
            return $this->error("合约地址无效");
        }
    }

    public function upload_icon(Request $request){
        $upload_file = $request->file("file");

        //验证最大上传大小限制
        $maxSize = config('app.file_maxsize')*1024*1024;

        if($upload_file->getSize() >= $maxSize) {
            $return['errcode'] = 10000;
            $return['message'] = '文件大小超出最大限制';
            return $return;
        }

        $fileFolder = "coin_icon";
        $fileName = time().".".$upload_file->getClientOriginalExtension();
        $file = [
            'fileFolder'=>$fileFolder, // 上传到OSS的指定文件夹
            'fileName'=>$fileName, // 上传文件名（xxx.jpg）
            'filePath'=>$upload_file->getPath()."/".$upload_file->getFilename() // 上传文件对象
        ];
        $res = Helper::upload_to_oss($file);

        if($res){
            $return['errcode'] = 0;
            $return['message'] = '上传成功';
            $return['data'] = ['src'=>$res['src'],"width"=>$res['width'],'height'=>$res['height']];
            return $return;
        }
        else{
            $return['errcode'] = 10000;
            $return['message'] = '上传失败';
            return $return;
        }
    }

    //系统公告
    public function notice()
    {

        $conf = DB::table('conf')->lists('keyvalue','keyname');

        return view("manage.setting.notice",['conf'=>$conf]);
    }

    //账号维护
    public function admin()
    {
        $admininfo = Admin::getAdminInfo();
        $roleinfo = AdminRole::all();
        return view("manage.admin.admin",["admininfo"=>$admininfo,"roleinfo"=>$roleinfo]);
    }

    //权限设置
    public function set_admin_access(Request $request)
    {
        $admin = Admin::where("id",$request->id)->first();
        if($admin->role_id==0&&$request->role_id>0){
            $super_count = Admin::where("role_id",0)->where("id","<>",$admin->id)->count();
            if($super_count==0){
                return $this->error("必须保留一个超级管理员",FanweErrcode::MANAGE_SUPERADMIN_EXIST);
            }
        }
        $param = [
            'id'=>$request->id,
            'role_id'=>$request->role_id
        ];
        $res = Admin::setAccess($param);
        if($res){
            return $this->success();
        }else{
            return $this->error();
        }
    }

    //新增账号
    public function add_admin(Request $request)
    {
        if(!$request->username){
            return $this->error("请输入账号",FanweErrcode::MANAGE_ADMINNAME_NOT_EXIST);
        }
        if(!$request->password){
            return $this->error("请输入密码",FanweErrcode::MANAGE_ADMINPASSWORD_NOT_EXIST);
        }
        if($request->password != $request->check_password){
            return $this->error("两次输入的密码不一致",FanweErrcode::MANAGE_ADMINPWDCHECK_ERROR);
        }
        $adminInfo = Admin::where(['username'=>$request->username])->first();
        if($adminInfo){
            return $this->error("该账号已存在",FanweErrcode::MANAGE_ADMINNAME_EXIST);
        }else{
            $param = [
                'username'=>$request->username,
                'password'=>$request->password,
                'role_id'=>$request->role_id
            ];
            $res = Admin::addAdmin($param);
            if($res){
                return $this->success();
            }else{
                return $this->error();
            }
        }
    }

    //删除账号
    public function del_admin(Request $request)
    {
        $admin = Admin::where("id",$request->id)->first();
        $super_count = Admin::where("role_id",0)->where("id","<>",$admin->id)->count();
        if($super_count==0){
            return $this->error("必须保留一个超级管理员",FanweErrcode::MANAGE_SUPERADMIN_EXIST);
        }

        $param = [
            'id'=>$request->id
        ];
        $res = Admin::delAdmin($param);
        if($res){
            return $this->success();
        }else{
            return $this->error();
        }
    }

    //更改密码
    public function update_admin_pwd(Request $request)
    {
        if(!$request->new_password){
            return $this->error("请输入新密码",FanweErrcode::MANAGE_NEWPASSWORD_NOT_EXIST);
        }
        if(!$request->check_password){
            return $this->error("请输入确认密码",FanweErrcode::MANAGE_CHECKPASSWORD_NOT_EXIST);
        }
        if($request->new_password != $request->check_password){
            return $this->error("两次输入的密码不一致",FanweErrcode::MANAGE_ADMINPWDCHECK_ERROR);
        }
        $param = [
            'id'=>$request->id,
            'password'=>$request->new_password
        ];
        $res = Admin::updateAdminPwd($param);
        if($res){
            return $this->success();
        }else{
            return $this->error();
        }
    }

    //矿池添加
    public function pool(Request $request)
    {
        $date = $request->input('date');
        $fixed = $request->input('fixed',0);
        if($date)
        {
            $dateStr = explode('~',$date);
            $begin_date = $dateStr[0];
            $end_date = $dateStr[1];
        }
        $mineLogs = MineLog::selectRaw('sum(amount) as total,coin_type')->where(function ($query) use($begin_date,$end_date,$fixed){
            if($begin_date)
            {
                $query->where('update_time',">",strtotime($begin_date." 00:00:00"));
            }
            if($end_date)
            {
                $query->where('update_time',"<",strtotime($end_date." 23:59:59"));
            }
            $query->where('mined',$fixed);
        })
          ->groupBy('coin_type')
          ->get()->keyBy('coin_type')->toArray();

        $coin_types = CoinType::select('coin_unit','id','icon','name')->get()->keyBy('id')->toArray();
        array_unshift($coin_types,[
            'id'=>0,
            'coin_unit'=>db_config('COIN_UNIT'),
            'icon'=>db_config('COIN_ICON')
        ]);
       $mine_total = MinePool::all()->keyBy('coin_type')->toArray();

        $lists = collect($coin_types)->map(function ($item)use($mineLogs,$mine_total){
            $item['total'] = number_format($mineLogs[$item['id']]['total'],8);
            $item['total_amount'] = $mine_total[$item['id']]['total_amount'];
            return $item;
        });

        return view("manage.setting.pool",['lists'=>$lists,'date'=>$date,'fixed'=>$fixed]);
    }

    public function pool_add(Request $request)
    {
        $change_type = $request->input('change_type');
        $amount = $request->input('amount');
        $memo = $request->input('memo');
        $coin_type = intval($request->input('coin_type'));
        DB::beginTransaction();
        try{
            Helper::changePool($amount,$change_type,$coin_type,$memo,'platform');
            DB::commit();
            return $this->success();
        }catch (\Exception $e){
            DB::rollback();
            return $this->error();
        }

    }

    public function price_log($api_param) {
        $two_week_ago = strtotime(date('Y-m-d',strtotime('-14days')));
        $coin_type = CoinType::where('api_param',$api_param)->first();
        $lists = PriceLog::where('api_param',$api_param)->orderBy('create_time','desc')->paginate(20);
        $price_logs = PriceLog::where('api_param',$api_param)->where('api_timestamp','>',$two_week_ago)->get()->toArray();
        $chart = [];
        collect($price_logs)->map(function ($item){
            $item['date'] = date('Y-m-d',$item['api_timestamp']);
            return $item;
        })->groupBy('date')
        ->map(function ($item,$key) use(&$chart){
            $item = $item->sortBy('api_timestamp');
            $day_start = $item->first();
            $day_end = $item->pop();
            $day_min = $item->min('last');
            $day_max = $item->max('last');
            $chart[$key] = [
                'day_start'=>$day_start['last'],
                'day_end'=>$day_end['last'],
                'day_min'=>$day_min,
                'day_max'=>$day_max
            ];
        });
        return view("manage.setting.price_log",['lists'=>$lists,'coin_type'=>$coin_type,'chart'=>$chart]);
    }

    //充值设置
    public function recharge(Request $request) {
        $address=$request->input('address','');
        $address_type = $request->input('address_type','1');
        $token_symbol = $request->input('token_symbol','');
        $lists = SubscribeToken::where(function ($query)use($address,$address_type,$token_symbol){
            if($address&&$address_type==1){
                $query->where('contract_address','like',"%{$address}%");
            }elseif ($address&&$address_type==2){
                $query->where('incharge_address','like',"%{$address}%");
            }
            if($token_symbol){
                $query->where('token_symbol',$token_symbol);
            }
        })->paginate(20);
        return view("manage.setting.recharge",['lists'=>$lists,'address'=>$address,'address_type'=>$address_type,'token_symbol'=>$token_symbol]);
    }
    //修改充值设置
    public function recharge_save(Request $request) {
        $id = $request->input('id');
        $subToken = SubscribeToken::find($id);
        if(!$subToken){
            return $this->error('无此记录');
        }
        $incharge_address = $request->input('incharge_address');
        $incharge_rate = $request->input('incharge_rate');
        if(!$incharge_address){
            return $this->error('请输入充币地址');
        }
        if(!$incharge_rate){
            return $this->error('请输入兑换基础币比率');
        }
        $subToken->incharge_address = $incharge_address;
        $subToken->incharge_rate = $incharge_rate;
        $result = $subToken->save();
        if($result){
            return $this->success();
        }else{
            return $this->error();
        }
    }
}
