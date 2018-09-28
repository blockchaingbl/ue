<?php
namespace App\Open\Services\App\Task;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\CpLog;
use App\Open\Services\FanweAuthService;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class BannerApi extends FanweAuthService
{

    /**
     * @name index
     * @description 获取任务项
     * @param 无，只需要授权有效即可
     * @return banner_list
     * [
     * "name" => 名称,
     * "desc" => 说明,
     * "income_cp" => 获取算力值,
     * "cp_total" => 累计获取算力值,
     * "url" => 跳转链接,
     * "open" => 是否开通,
     * "hide" => 是否隐藏
     * ]
     */
    public function index($param)
    {
        $user = $this->user;

//        Cache::forget("cp_o2o:".$user->id);
//        Cache::forget("cp_sign:".$user->id);
//        Cache::forget("cp_invite:".$user->id);

//        $cp_o2o = Cache::get("cp_o2o:".$user->id);
//        if(!$cp_o2o)
//        {
            $cp_o2o = CpLog::where(["user_id"=>$user->id,"type"=>0,"api"=>"o2o"])->sum("cp_amount");
//            Cache::put("cp_o2o:".$user->id,$cp_o2o,10);
//        }
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
        $cp_study = CpLog::where(["user_id"=>$user->id,"type"=>1,"api"=>"study"])->sum("cp_amount");

        $o2o = [
            "name"=>"商城交易",
            "desc"=>"前往商城交易返还相应算力",
            "income_cp"=>0,
            "cp_total"=>intval($cp_o2o),
            "url"=>"http://o2oxsl.fanwe.net/wap/index.php?from=wap",
            "open"=>1,
            "hide"=>0,
        ];
        $game = [
            "name"=>"我的国",
            "desc"=>"新用户赠送500算力",
            "income_cp"=>0,
            "cp_total"=>0,
            "url"=>"",
            "open"=>1,
            "hide"=>1,
        ];
        $sign = [
            "name"=>"每日登录",
            "desc"=>"登录即可得".db_config("SIGN_CP")."算力",
            "income_cp"=>db_config("SIGN_CP"),
            "cp_total"=>intval($cp_sign),
            "url"=>"",
            "open"=>1,
            "hide"=>0,
        ];
        $invite = [
            "name"=>"邀请好友",
            "desc"=>"每邀请一名好友完成注册可得".db_config("INVITE_CP")."算力",
            "income_cp"=>db_config("INVITE_CP"),
            "cp_total"=>intval($cp_invite),
            "url"=>"",
            "open"=>1,
            "hide"=>0,
        ];
        $study =[
            "cp_total"=>intval($cp_study),
            'url'=>'https://shop.bmweixin.com/app/index.php?thumburl=httpstech1tech2tech2shoptech3bmweixintech3comtech2attachmenttech2imagestech220tech22018tech209tech2RvO9sQ8K3KqxXKq9yXzN6qIy0XV9oktech3png&c=entry&id=64&do=video_detail&publishtime=1536141225&i=20&m=tech_video&videoid=httptech1tech2tech2mpvtech3videocctech3nettech2e620f0b882tech2ctech2e620f0b882cdc9d885768cc049c10d4c_1tech3mp4'
        ];
        $banner_list["o2o"] = $o2o;
        $banner_list["game"] = $game;
        $banner_list["sign"] = $sign;
        $banner_list["invite"] = $invite;
        $banner_list['study'] = $study;
        $this->setData("banner_list",$banner_list);
        return $this->success();
    }

    /**
     * @name study
     * @description study
     * @param 无，只需要授权有效即可
     * @return array
     */
    public function study()
    {
        $user = $this->user;
        $today_start = date('Y-m-d').' 00:00:00';
        $today_end = date('Y-m-d').' 23:59:59';
        $cp_study = CpLog::where(["user_id"=>$user->id,"type"=>1,"api"=>"study"])
        ->where('create_time','>',$today_start)
        ->where('create_time','<',$today_end)
        ->sum("cp_amount");
        if($cp_study>=1)
        {
            return $this->error('已经领取');
        }else{
            Helper::GrantCp($user->id,1,1,'study');
            return  $this->success();
        }
    }

}