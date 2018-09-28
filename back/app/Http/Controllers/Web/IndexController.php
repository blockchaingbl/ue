<?php
namespace App\Http\Controllers\Web;


use App\Helper;
use App\Http\Models\FreezeLog;
use App\Http\Models\GuxiangTokenDuobaoBonus;
use App\Http\Models\GuxiangTokenTransaction;
use App\Http\Models\LockTransferLog;
use App\Http\Models\Mingren;
use App\Http\Models\MingrenTokenDuobaoBonus;
use App\Http\Models\MingrenTokenTransaction;
use App\Http\Models\PointLog;
use App\Http\Models\Pony;
use App\Http\Models\Web\User;
use App\Library\CurlInvoker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class IndexController extends BaseController
{

    //首页
    public function index($id)
    {
        if(config("app.debug"))
        {
            $this->fetchReferuser($id);
            return view("index");
        }
        else
        {
            if(Helper::isApp()||$_REQUEST['debug']==1)
            {
                $this->fetchReferuser($id);
                return view("index");
            }
            else
            {
                echo '<div style="width:100%;margin-top:50px;text-align:center;font-size:50px;">请使用APP打开</div>';
                exit;
            }
        }
    }

    //APP初始化
    public function init(Request $request)
    {
        $data['site_url'] = "http://www.".config("app.route_domain")."?v=".config("app.version");
        //登录页与挖矿页面为退出的基础页
        $data['top_url'] = [
            "http://www.".config("app.route_domain")."/?v=".config("app.version")."#/login",
            "http://www.".config("app.route_domain")."/?v=".config("app.version")."#/mine/center"
        ];
//        $data['site_url'] = "http://192.168.10.93:2222?v=".config("app.version"); //本地调试
        $data['statusbar_color'] = "#ffffff";
        $data['font_color'] = 1;
        $data['reload_time'] = 1800;

        //开始处理app升级业务
        $sdk_type = $request->input("sdk_type");
        if($sdk_type=="android")
        {
            $sdk_version_name = $request->input("sdk_version");
            if($sdk_version_name<config("app.android.version_name"))
            {
                //有新版本，需升级
                $data['version']['has_upgrade'] = 1;
                $data['version']['filename'] = config("app.android.download_url");
                if($data['version']['filename'])$data['version']['hasfile'] = 1;
                $data['version']['forced_upgrade'] = config("app.android.forced_upgrade");
                $data['version']['serverVersion'] = config("app.android.version_name");
                $data['version']['android_upgrade'] = config("app.android.upgrade_des");
            }
        }
        else
        {
            $sdk_version_name = $request->input("sdk_version");
            if($sdk_version_name<config("app.ios.version_name"))
            {
                //有新版本，需升级
                $data['version']['has_upgrade'] = 1;
                $data['version']['ios_down_url'] = config("app.ios.download_url");
                if($data['version']['ios_down_url'])$data['version']['hasfile'] = 1;
                $data['version']['forced_upgrade'] = config("app.ios.forced_upgrade");
                $data['version']['serverVersion'] = config("app.ios.version_name");
                $data['version']['ios_upgrade'] = config("app.ios.upgrade_des");
            }
        }

        return $data;
    }

    //APP下载
    public function download()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $download_url = "";
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad'))
        {
            //ios
            $download_url = config("app.ios.download_url");
        }
        if(strpos($agent, 'android'))
        {
            //android
            $download_url = config("app.android.download_url");
        }
        return redirect($download_url);
    }

    public function recover(Request $request){
        exit;
        $key = $request->input('key');
        if($key!=='fdsgdfdf')
        {
            exit;
        }
        $lists = LockTransferLog::where('free',0)->get();
        $lists->map(function ($val){
            $freeze_log = FreezeLog::find($val->freeze_log_id);
            $free_time = $freeze_log->vc_done_amount/($val->amount/$val->lock_time);
            $remain_time = $val->lock_time - $free_time;
            $update_data = [
                'less_amount'=>$val->amount - $freeze_log->vc_done_amount,
                'remain_time'=>$remain_time,
                'last_release_time'=>date('Y-m-d H:i:s',strtotime($val->create_time)+(86400*$free_time))
            ];
            $effect_num = LockTransferLog::where('id',$val->id)
            ->update($update_data);

            dump($effect_num);
        });
        dd($lists);
    }
}
