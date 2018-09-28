<?php
namespace App\Open\Services\App\User;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CoinType;
use App\Http\Models\Common\SmsCode;
use App\Http\Models\Connect;
use App\Http\Models\CpLog;
use App\Http\Models\ExpendLog;
use App\Http\Models\FlowApply;
use App\Http\Models\FreeLog;
use App\Http\Models\FreezeLog;
use App\Http\Models\IncomeLog;
use App\Http\Models\LockTransferAuth;
use App\Http\Models\PromoterReward;
use App\Http\Models\SugarAuth;
use App\Http\Models\UserAsset;
use App\Http\Models\UserWallet;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class AccountApi extends FanweAuthService
{

    /**
     * @name info
     * @description 获取用户信息
     * @param 无，只需要授权有效即可
     * @return account_info
     * [
        "id"=> 用户ID,
        "address"=> 钱包地址,
        "privatekey"=> 私钥,
        "username"=> 用户名,
        "mobile"=>手机号,
        "pid"=>推荐人ID,
        "avatar"=>头像,
        "vc_total_format"=> 可用金额(带单位),
        "vc_total"=> 可用金额,
        "vc_nomal_format"=> 可交易金额(带单位),
        "vc_nomal"=> 可交易金额,
        "vc_untrade_format"=>不可交易金额(带单位),
        "vc_untrade"=>不可交易金额,
        "vc_freeze_format"=>冻结金额(带单位),
        "vc_freeze"=>冻结金额,
     *  "vc_freeze_nomal"=> 冻结的可交易金额,
        "vc_freeze_nomal_format"=> 冻结的可交易金额(带单位),
        "vc_freeze_untrade"=> 冻结的不可交易金额,
        "vc_freeze_untrade_format"=>冻结的不可交易金额（带单位）,
     *  "vc_amount" => 账户总额（可用+冻结）,
        "vc_amount_format" => 账户总额（可用+冻结）(带单位),
        "fvc_trade" => 交易获得的金额,
        "fvc_bonus" => 非交易获得的金额,
        "cp_trade" => 交易获得的算力,
        "cp_bonus" => 非交易获得的算力,
        "cp_total" => 算力总额,
        "cp_rank" => 算力排行,
        "invite_num" => 邀请好友数,
        "invite_code" => 邀请码,
        "register_no" => 第几位注册用户,
        "otc_auth" => OTC是否授权
    ];
     *
     */
    public function info($param)
    {
        $user = $this->user;

//        $cp_sign = Cache::get("cp_sign:".$user->id);
//        if(!$cp_sign)
//        {
            $cp_sign = CpLog::where(["user_id"=>$user->id,"type"=>1,"api"=>"sign"])->sum("cp_amount");
//            Cache::put("cp_sign:".$user->id,$cp_sign,10);
//        }
//        $cp_invite = Cache::get("cp_invite:".$user->id);
//        if(!$cp_invite)
//        {
            $cp_invite = CpLog::where(["user_id"=>$user->id,"type"=>1,"api"=>"invite"])->sum("cp_amount");
//            Cache::put("cp_invite:".$user->id,$cp_invite,10);
//        }
        $has_wallet = UserWallet::where('user_id',$user->id)->count();
        $sugar_auth = SugarAuth::where(['user_id'=>$user->id,'status'=>1])->count();
        $lock_transfer_auth = LockTransferAuth::where(['user_id'=>$user->id,'status'=>1])->count();
        $account_info =  [
            "id"=>$user->id,
            "address"=>$user->address,
            "privatekey"=>$user->privatekey,
            "username"=>$user->username,
            "mobile"=>$user->mobile,
            "pid"=>$user->pid,
            "avatar"=>img($user->avatar,200,200),
            "vc_total"=> Helper::formatCoin($user->vc_total,1),
            "vc_total_format"=> Helper::formatCoin($user->vc_total),
            "vc_normal"=>Helper::formatCoin($user->vc_normal,1),
            "vc_normal_format"=>Helper::formatCoin($user->vc_normal),
            "vc_untrade"=>Helper::formatCoin($user->vc_untrade,1),
            "vc_untrade_format"=>Helper::formatCoin($user->vc_untrade),
            "vc_freeze"=>Helper::formatCoin($user->vc_freeze,1),
            "vc_freeze_format"=>Helper::formatCoin($user->vc_freeze),
            "vc_freeze_nomal"=>Helper::formatCoin($user->vc_freeze_nomal,1),
            "vc_freeze_nomal_format"=>Helper::formatCoin($user->vc_freeze_nomal),
            "vc_freeze_untrade"=>Helper::formatCoin($user->vc_freeze_untrade,1),
            "vc_freeze_untrade_format"=>Helper::formatCoin($user->vc_freeze_untrade),
            "vc_amount" => Helper::formatCoin($user->vc_total+$user->vc_freeze,1),
            "vc_amount_format" => Helper::formatCoin($user->vc_total+$user->vc_freeze),
            "fvc_trade" => Helper::formatCoin($user->fvc_trade,1),
            "fvc_bonus" => Helper::formatCoin($user->fvc_bonus,1),
            "cp_trade" => intval($user->cp_trade),
            "cp_bonus" => intval($user->cp_bonus),
            "cp_total"=> intval($user->cp_total),
            "cp_sign" => intval($cp_sign),
            "cp_invite" => intval($cp_invite),
            "cp_rank"=> User::where("cp_total",">=",intval($user->cp_total))->count(),
            "invite_num"=> User::where("pid",$user->id)->count(),
            "invite_code"=>$user->invite_code,
            "register_no"=>User::where("id","<",$user->id)->count()+1,
            'security'=>$user->security,
            'has_wallet'=>$has_wallet,
            'otc_auth'=>$user->otc_auth,
            'otc_auth_type'=>$user->otc_auth_type,
            'limit_day'=>$user->limit_day,
            'sugar_auth'=>$sugar_auth,
            'mobile_code'=>$user->mobile_code,
            'lock_transfer_auth'=>$lock_transfer_auth,
            'attached'=>$user->attached
        ];
        $this->setData("account_info",$account_info);
        return $this->success();
    }


    /**
     * @name friend
     * @description 好友的基本信息
     * @param $param user_id:好友ID
     * @return [
     *  'id'
     *  'username'
     *  'avatar'
     * ]
     */
    public function friend($param){
        $user_id = $param['user_id'];


        $user = User::leftJoin("user_follow",function($join){
            $join->on('user_follow.follow_user_id','=',"user.id")->where("user_follow.user_id",'=',$this->user->id);
        })->leftJoin("user_follow as user_fans",function($join){
            $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
        })->whereRaw('fanwe_user_fans.id is not null')->whereRaw('fanwe_user_follow.id is not null')
            ->where("user.id",$user_id)
            ->select("user.id","user.username","user.avatar","user.create_time","user_follow.id as user_follow_id","user_fans.id as user_fans_id")->orderBy("user.id","asc")->first();

        if(!$user)
            return $this->error("非法的好友请求");

        $this->setData("user",$user);
        return $this->success();
    }

    /**
     * @name income
     * @description 获取用户收入明细
     * @param
     * coin_type：币类型（0表示基础币），不传参则默认获取基础币
     * type：类型(mine：挖矿；transfer：转账；purchase：购买；allot：划拨)，不传参则获取全部
     * page：页数
     * @return income_log
     * [
     * "id" => 记录ID,
     * "coin_type" => 币类型,
     * "user_id" => 用户ID,
     * "detail" => 详细内容
     * "vc_amount" => 金额,
     * "vc_normal" => 可交易,
     * "vc_untrade" => 不可交易,
     * "create_time" => 时间,
     * "type" => 收入类型,
     * "relate" => 相关类型关联的数据详细记录ID
     * ]
     */
    public function income($param)
    {
        $user = $this->user;
        $income_log = IncomeLog::getIncomeLog($user->id,$param);
        //格式化数据
        $income_log = $income_log->toArray()["data"];
        $income_data = [];
        foreach($income_log as $item)
        {
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["vc_normal"] = Helper::formatCoin($item["vc_normal"],1);
            $item["vc_untrade"] = Helper::formatCoin($item["vc_untrade"],1);
            array_push($income_data,$item);
        }
        $this->setData("income_log",$income_data);
        return $this->success();
    }

    /**
     * @name expend
     * @description 获取用户支出明细
     * @param
     * coin_type：币类型（0表示基础币），不传参则默认获取基础币
     * type：类型(withdraw：提现；transfer：转账；sale：出售；exchange：兑换)，不传参则获取全部
     * page：页数
     * @return expend_log
     * [
     * "id" => 记录ID,
     * "coin_type" => 币类型,
     * "user_id" => 用户ID,
     * "detail" => 详细内容
     * "vc_amount" => 金额,
     * "vc_normal" => 可交易,
     * "vc_untrade" => 不可交易,
     * "create_time" => 时间,
     * "type" => 支出类型,
     * "relate" => 相关类型关联的数据详细记录ID
     * ]
     */
    public function expend($param)
    {
        $user = $this->user;
        $expend_log = ExpendLog::getExpendLog($user->id,$param);
        //格式化数据
        $expend_log = $expend_log->toArray()["data"];
        $expend_data = [];
        foreach($expend_log as $item)
        {
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["vc_normal"] = Helper::formatCoin($item["vc_normal"],1);
            $item["vc_untrade"] = Helper::formatCoin($item["vc_untrade"],1);
            array_push($expend_data,$item);
        }
        $this->setData("expend_log",$expend_data);
        return $this->success();
    }

    /**
     * @name freeze
     * @description 获取用户冻结明细
     * @param
     * coin_type：币类型（0表示基础币），不传参则默认获取基础币
     * type：类型(withdraw：提现；sale：出售；manual：人工)，不传参则获取全部
     * page：页数
     * @return freeze_log
     * [
     * "id" => 记录ID,
     * "coin_type" => 币类型,
     * "user_id" => 用户ID,
     * "detail" => 详细内容
     * "vc_amount" => 金额,
     * "vc_normal" => 可交易,
     * "vc_untrade" => 不可交易,
     * "freeze_time" => 冻结时间,
     * "free_time" => 解冻时间,
     * "type" => 支出类型,
     * "relate" => 相关类型关联的数据详细记录ID
     * ]
     */
    public function freeze($param)
    {
        $user = $this->user;
        $freeze_log = FreezeLog::getFreezeLog($user->id,$param);
        //格式化数据
        $freeze_log = $freeze_log->toArray()["data"];
        $freeze_data = [];
        foreach($freeze_log as $item)
        {
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["vc_normal"] = Helper::formatCoin($item["vc_normal"],1);
            $item["vc_untrade"] = Helper::formatCoin($item["vc_untrade"],1);
            $item["vc_done_amount"] = Helper::formatCoin($item["vc_done_amount"],1);
            $item["vc_done_normal"] = Helper::formatCoin($item["vc_done_normal"],1);
            $item["vc_done_untrade"] = Helper::formatCoin($item["vc_done_untrade"],1);
            $item["vc_lock_normal"] = Helper::formatCoin($item["vc_lock_normal"],1);
            array_push($freeze_data,$item);
        }
        $this->setData("freeze_log",$freeze_data);
        return $this->success();
    }

    /**
     * @name sign
     * @description 每日签到
     * @param 无，只需要授权有效即可
     * @return
     * 成功：[
     *  "cp_amount" => 获得算力值,
     *  "fvc_amount" => 获得奖金
     * ]
     * 失败：返回错误码及提示
     */
    public function sign($param)
    {
        $user = $this->user;
        $cp_amount = db_config("SIGN_CP");
        if(!$cp_amount)
        {
            return $this->error("每日签到平台无返利",FanweErrcode::SIGN_NOT_REBATE);
        }
        $nowDate = date("Y-m-d");
        $signFinalDate = CpLog::where(["user_id"=>$user->id,"type"=>1,"api"=>"sign"])->max("create_time");
        $signFinalDate = date("Y-m-d",strtotime($signFinalDate));
        if($signFinalDate>=$nowDate)
        {
            return $this->error("今天已经签到过",FanweErrcode::USER_SIGN_EXIST);
        }
        DB::beginTransaction();
        try{
            Helper::GrantCp($user->id,1,$cp_amount,"sign");
            DB::commit();
            $this->setData("cp_amount",$cp_amount);
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name editinfo
     * @description 修改资料
     * @param
     * username：名称
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function editinfo($param)
    {
        $user = $this->user;
        $username = $param["username"];
        if(!$username)
        {
            return $this->error("请输入用户名",FanweErrcode::USERNAME_NOT_EXSITS);
        }
        if(!User::checkNickname($username))
        {
            return $this->error("用户名只允许中文、大小写字母、数字，最少2个，最多8个字符",FanweErrcode::USERNAME_FORMAT_ERROR);
        }
        if(User::getUserByUserName($username))
        {
            return $this->error("用户名已存在",FanweErrcode::USERNAME_EXSITS);
        }
        DB::beginTransaction();
        try{
            $user = User::where("id",$user->id)->first();
            $user->username = $username;
            $user->save();
            DB::commit();
            return $this->success();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * @name asset
     * @description 各币种金额信息
     * @param
     * coin_type : 单币种信息
     * single :是否查询单币种信息 1是
     * page:页数
     * @return asset
     * [
     * "id" => 记录ID,
     * "user_id" => 用户ID,
     * "vc_total" => 可用的虚拟资产数量,
     * "vc_freeze" => 冻结的虚拟币数量,
     * "coin_type" => [
     *     "icon" => 币种图片信息
     *     "name" =>币种名称
     *     "price" => 价格
     *     "price_unit" => 价格单位
     * ],
     * ]
     */
    public function asset($param)
    {
        $user = $this->user;
        if($param['single']==1){
            $coin_type = $param['coin_type'];
            $auth = LockTransferAuth::where(['coin_type'=>0,'user_id'=>$user->id,'status'=>1])->first();
            if($coin_type>0){
                $data = CoinType::find($coin_type);
                $asset  = UserAsset::where('user_id',$user->id)->where('coin_type',$data->id)->first();
                $data = [
                    'vc_total'=>Helper::formatCoin($asset->vc_total,1),
                    'vc_freeze' => Helper::formatCoin($asset->vc_freeze,1),
                    'coin_type' => [
                        'id' => $data->id,
                        'icon' => $data->icon,
                        'coin_unit' => $data->coin_unit,
                        'price' =>Helper::formatRMB(($data->price)),
                        'min_withdraw'=>$data->min_withdraw,
                        'withdraw_rate'=>$data->withdraw_rate,
                        'withdraw_open'=>$data->withdraw_open,
                        'incharge_open'=>$data->incharge_open,
                        'limit_rate'=>$data->limit_rate,
                        'lock_trans_auth'=> $auth ? 1 : 0
                    ]
                ];
            }else{
                $data = UserAsset::where('user_id',$user->id)->where('coin_type',0)->first()->toArray();
                $data['coin_type'] = [];
                $data['id']  = 0;
                $data['coin_type']['id'] = 0;
                $data['coin_type']['icon'] = db_config('COIN_ICON');
                $data['coin_type']['coin_unit'] = db_config('COIN_UNIT');
                $data['coin_type']['price'] = Helper::formatRMB(db_config('COIN_PRICE'));
                $data['coin_type']['lock_trans_auth'] = $auth ? 1 : 0;
                $data['coin_type']['limit_rate'] = db_config('WITHDRAW_LIMIT_RATE');

            }
        }else{
            $size = 10;
            if($param['page'] <=1){
                $size--;
            }
            $data = CoinType::where("status",1)->paginate($size);
            $auths = LockTransferAuth::where(['user_id'=>$user->id,'status'=>1])->get()->keyBy('coin_type')->toArray();
            if($data){
                $data = $data->toArray()['data'];
                $data = collect($data)->map(function ($val)use($user,$auths){
                    $asset = UserAsset::where('user_id',$user->id)->where('coin_type',$val['id'])->first();
                    $tmp['id'] = $val['id'];
                    $tmp['vc_amount'] = Helper::formatCoin($asset->vc_total+$asset->vc_freeze,1);
                    $tmp['vc_total'] = Helper::formatCoin($asset->vc_total,1);
                    $tmp['vc_freeze'] = Helper::formatCoin($asset->vc_freeze,1);
                    $tmp['coin_type'] = [
                        'id' => $val['id'],
                        'icon' => $val['icon'],
                        'coin_unit' => $val['coin_unit'],
                        'price' =>Helper::formatRMB(($val['price'])),
                        'min_withdraw'=>$val['min_withdraw'],
                        'withdraw_rate'=>$val['withdraw_rate'],
                        'withdraw_open'=>$val['withdraw_open'],
                        'incharge_open'=>$val['incharge_open'],
                        'lock_trans_auth'=>$auths[$val['id']] ? 1 : 0,
                        'limit_rate'=>$val['limit_rate']
                    ];
                    return $tmp;
                })->toArray();
            }else{
                $data = [];
            }

            if($size<10){
                $money = User::where('id',$user->id)->first();
                $auth = LockTransferAuth::where(['coin_type'=>0,'user_id'=>$user->id,'status'=>1])->first();
                $default['coin_type'] = [];
                $default['id'] = '0';
                $default['vc_amount'] = Helper::formatCoin($money['vc_total']+$money['vc_freeze'],1);
                $default['coin_type']['id'] = 0;
                $default['coin_type']['limit_rate'] = db_config('WITHDRAW_LIMIT_RATE');
                $default['coin_type']['icon'] = db_config('COIN_ICON');
                $default['coin_type']['coin_unit'] = db_config('COIN_UNIT');
                $default['coin_type']['price'] = Helper::formatRMB(db_config('COIN_PRICE'));
                $default['coin_type']['lock_trans_auth'] = $auth ? 1 : 0;
                $data = array_merge([$default],$data);

            }
        }
        $this->setData('asset',$data);
        return $this->success();
    }

    /**
     * @name security
     * @description 修改密码
     * @param
     * verifycode.:验证码
     * password :新密码
     * check_password:确认密码
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function security($param)
    {
        $user = $this->user;
        $mobile = $user->mobile;
        $password = $param['password'];
        $confirm = $param['check_password'];
        $verifycode = $param['verifycode'];
        if(!$verifycode)
        {
            return $this->error("请输入手机验证码",FanweErrcode::MOBILE_CODE_EMPTY);
        }
        if(!SmsCode::checkRegisterCode($mobile,$verifycode))
        {
            return $this->error("手机验证码无效",FanweErrcode::MOBILE_CODE_ERROR);
        }
        if(!$password){
            return $this->error("密码为空",FanweErrcode::USER_SECURITY_NOT_EXSITS);
        }
        if($password!=$confirm){
            return $this->error("两次密码输入不相等",FanweErrcode::USER_SECURITY_NOT_CONFIRM);
        }
        if(md5($password) == $user->security){
            return $this->error("新密码不可与旧密码相同",FanweErrcode::USER_SECURITY_SAME);
        }
        $user->security = md5($password);
        $user->save();
        return $this->success('设置成功');
    }

    /**
     * @name invite
     * @description 邀请的人的信息
     * @param
     * page: 页数

     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function invite()
    {
        $user = $this->user;
        $invite_users = User::where('pid','=',$user->id)->paginate(10);
        $data = $invite_users->toArray()['data'];
        $this->setData('total',$invite_users->total());
        $this->setData('invite',$data);
        return $this->success();
    }

    /**
     * @name flow
     * @description 申请成为流量主
     * @param
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function flow()
    {
        $user = $this->user;
        if($user->flow_status==1){
            return $this->error('申请中,请耐心等待',FanweErrcode::ALREADY_APPLY);
        }elseif ($user->flow_status==2){
            return $this->error('已经成为流量主',FanweErrcode::ALREADY_FLOW);
        }
        DB::beginTransaction();
        try{
            $flow = new FlowApply();
            $flow->create_time = date('Y-m-d H:i:s');
            $flow->status = 0;
            $flow->user_id = $user->id;
            $flow->save();
            $user->flow_status = 1;
            $user->save();
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return $this->error($e->getMessage());
        }

        $this->setData('flow_status',$user->flow_status);
        return $this->success();
    }

    /**
     * @name achievement
     * @description 业绩
     * @param
     * date : YYYY-MM
     * @return
     * 成功：null
     * 失败：返回错误码及提示
     */
    public function achievement($param)
    {
        $user = $this->user;
        $date = $param['date'];
        $lists = PromoterReward::with('invite_user')->
        where(function($query) use($date,$user){
            $query->where('user_id',$user->id);
            $query->where('create_date_month','=',$date);
        })->orderBy('id','desc')
        ->paginate(10);

        $curr_sum = PromoterReward::where(function($query) use($date,$user){
            $query->where('user_id',$user->id);
            $query->where('create_date_month','=',$date);
        })->sum('total_amount');


        $lists = $lists->toArray()['data'];
        $this->setData('lists',$lists);
        $this->setData('curr_sum',$curr_sum);
        return $this->success();
    }

    /**
     * @name free
     * @description 获取用户释放明细
     * @param
     * coin_type：币类型（0表示基础币），不传参则默认获取基础币
     * type：类型(sugar 糖果 lock_transfer 锁仓)，不传参则获取全部
     * page：页数
     * @return free_log
     * [
     * "id" => 记录ID,
     * "coin_type" => 币类型,
     * "user_id" => 用户ID,
     * "detail" => 详细内容
     * "vc_amount" => 金额,
     * "vc_normal" => 可交易,
     * "vc_untrade" => 不可交易,
     * "create_time" => 时间,
     * "type" => 收入类型,
     * "relate" => 相关类型关联的数据详细记录ID
     * ]
     */
    public function free($param)
    {
        $user = $this->user;
        $free_log = FreeLog::where(function ($query)use($user,$param){
            $query->where('user_id',$user->id);
            if($param['type']){
                $query->where('type',$param['type']);
            }
            $query->where('coin_type',$param['coin_type']);
        })->orderBy('id','desc')->paginate(20);
        //格式化数据
        $free_log = $free_log->toArray()["data"];
        $data = [];
        foreach($free_log as $item)
        {
            $item["vc_amount"] = Helper::formatCoin($item["vc_amount"],1);
            $item["vc_normal"] = Helper::formatCoin($item["vc_normal"],1);
            $item["vc_untrade"] = Helper::formatCoin($item["vc_untrade"],1);
            array_push($data,$item);
        }

        $this->setData("free_log",$data);
        return $this->success();
    }

    public function huifu()
    {
        $lists = Connect::where('user_id',$this->user->id)->orderBy('id','desc')->paginate(20)->toArray()['data'];
        foreach($lists as $k=>$item)
        {
            $item['image'] = img($item['image'],64,64);
            $lists[$k] = $item;
        }
        $this->setData("huifus",$lists);
        return $this->success();
    }






}
