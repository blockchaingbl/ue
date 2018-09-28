<?php
namespace App\Open\Services\App\Cms;

use App\Helper;

use App\Http\Models\Connect;
use App\Open\Services\FanweAuthService;



class ConnectApi extends FanweAuthService
{

    /**
     * @name store
     * @description 填写反馈内容
     * @param
     * content
     * image
     * @return [
     * 成功 null
     * 失败 错误码和提示
     * ]
     */
    public function store($param)
    {
        $user = $this->user;
        $count = Connect::where(function ($query)use($user){
            $query->where('user_id',$user->id);
            $time = date('Y-m-d H:i:s',time()-86400);
            $query->where('create_time','>',$time);
            $query->where('create_time','<',date('Y-m-d H:i:s'));
        })->count();
        if($count>=5){
            return $this->error('反馈过于频繁,24小时内最多提交5次');
        }
        if(mb_strlen($param['content'],'utf-8')<10)
        {
            return $this->error('请填写10字以上反馈内容');
        }
        $connect = new Connect();
        $connect->user_id = $user->id;
        $connect->image = $param['image'];
        $connect->content = $param['content'];
        $connect->create_time = date('Y-m-d H:i:s');
        $connect->save();
        return $this->success();
    }

    /**
     * @name about
     * @description 填写反馈内容
     * @param
     * @return  array
     * about
     * ]
     */
    public function about($param)
    {
        $this->setData('about',db_config($param['config']));
        return $this->success();
    }


    /**
     * @name connect
     * @description connect
     * @param
     * @return  array
     * connect
     * ]
     */
    public function connect()
    {
        $this->setData('connect',db_config("CONNECT"));
        return $this->success();
    }

}