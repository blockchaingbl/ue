<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Models\Manage\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use OSS\OssClient;

/**
 * 管理权限校验的基础类
 *
 */
class AuthBaseController extends BaseController
{

    //标题，可重写
    protected $page_title;

    //导航，可重写
    protected $site_map;

    protected $eth;
	
    public function __construct(){
        parent::__construct();
    	//注册权限校验中间件
        $this->middleware('auth.manage',['except' => ['logout']]);

        //共享登录帐号信息
        View::share("admin",Auth::user());

        //设置当前菜单与site_map导航
        $this->page_title = config("app.name");

        //导航
        $this->site_map = [];

        $nav_name = config("manage.nav_name");

        $web_nav_config = config("manage.nav");

        if(!config("app.otc"))
        {
            //otc关闭
            unset($nav_name["transaction"]);
            unset($web_nav_config["transaction"]);
        }
        if(!config('app.cms')){
            unset($nav_name["cms"]);
            unset($web_nav_config["cms"]);
        }
        $web_nav = [];
        $web_nav_name = [];

        //过滤无权限的菜单项,重组菜单
        $admin = Auth::user();
        if($admin)
        {
            foreach($web_nav_config as $key=>$group)
            {
                $group_key = '';
                foreach($group as $route_as=>$route)
                {
                    if($admin->check_auth($route_as))
                    {
                        //使用首个通过授权校验的route_as作为分组的key
                        if($group_key=="")$group_key=$route_as;
                        $web_nav[$group_key][$route_as] = $web_nav_config[$key][$route_as];
                        $web_nav_name[$group_key] = $nav_name[$key];
                    }
                }
            }
        }
        View::share("web_nav_name",$web_nav_name);
        $web_nav_shortcut = config("manage.nav_shortcut");
        //过滤无权限的快捷菜单
        if($admin)
        {
            foreach($web_nav_shortcut as $route_as=>$nav)
            {
                if(!$admin->check_auth($route_as))
                {
                    unset($web_nav_shortcut[$route_as]);
                }
            }
        }
        $current_route = $GLOBALS['app']->getCurrentRoute();
        $route_name = $current_route['as'];
        if($admin)
        {
            //登录成功，检测权限
            if(!$admin->check_auth($route_name))
            {
                echo json_encode($this->error("权限不足"),JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        $route_name_arr = explode(".",$route_name);
        $group_key = $route_name_arr[0]; //第一个占位为分组（顶部的key）

        //当分组的主路由无权限时，由本分组内的第一顺序路由作为分组名
        if(!isset($web_nav[$group_key]))
        {
            foreach($web_nav as $k=>$v)
            {
                foreach($v as $kk=>$vv)
                {
                    if($kk==$route_name)
                    {
                        $group_key = $k;
                        break;
                    }
                }
            }
        }



        //二级菜单
        if(isset($web_nav[$group_key][$route_name]))
        {
            $route_second = $web_nav[$group_key][$route_name];
            $this->page_title = $this->page_title." - ".$route_second['name'];
            $this->site_map[$route_name] = $route_second;
        }
        if(!config('app.sugar'))
        {
            unset($web_nav['user']['sugar.auth']);
        }
        if(!config('app.lock_transfer'))
        {
            unset($web_nav['user']['lock_transfer.auth']);
        }

        View::share("page_title",$this->page_title);
        View::share('site_map', $this->site_map);

        //共享当前的路由名称
        View::share("route_name",$route_name); //当前路由名（key）
        View::share("group_key",$group_key); //分组的key

        //共享菜单配置
        View::share('web_nav', $web_nav);
        View::share('web_nav_shortcut', $web_nav_shortcut);

        //共享左侧菜单
        //array_shift($web_nav[$group_key]);
        View::share('left_nav', isset($web_nav[$group_key])?$web_nav[$group_key]:[]);
   	}

   	public function success($message = '操作成功',$data = '')
    {
        $return['errcode'] = FanweErrcode::SYSTEM_SUCCESS;
        $return['message'] = $message;
        $return['data'] = $data;
        return $return;
    }

    public function error($message = '操作失败',$code = FanweErrcode::SYSTEM_ERROR,$data = '')
    {
        $return['errcode'] = $code;
        $return['message'] = $message;
        $return['data'] = $data;
        return $return;
    }

    /**
     * 上传素材到OSS
     */
    public function upload_to_oss($file)
    {
            $res = Helper::upload_to_oss($file);
            if($res){
                return $this->success("上传成功",$res);
            }
            else{
                return $this->error("上传失败",FanweErrcode::MANAGE_OSSUPLOAD_ERROR);
            }
    }

    /**
     * 验证输入的验证码
     * @param Request $request
     * @param $captchaUuid uuid
     * @param string $type  类型
     * @return bool
     */
    public function checkCaptcha(Request $request ,$captchaUuid,$type = 'default')
    {
        $validaor = $this->getValidationFactory()->make($request->all(),[
            'captcha'=>'required|captcha:'.$captchaUuid
        ]);
        if($validaor->fails()){
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * 验证手机号是否合法
     */
    public function checkMobile($mobile)
    {
        $regx = "/^[1][34578][0-9]{9}$/";
        if(preg_match($regx,$mobile))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    protected function format_eth_address($address)
    {
        return "0x".substr($address,-40,40);
    }



}
