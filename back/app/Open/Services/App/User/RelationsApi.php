<?php
namespace App\Open\Services\App\User;

use App\Http\Models\UserFollow;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;

class RelationsApi extends FanweAuthService
{
    /**
     * @name friends
     * @description 好友列表
     * @param $param
     * @return user_list: [
     * {
     * id: 34,
     * username: "12334的",
     * create_time: "2018-05-02 19:17:56",
     * user_follow_id: 6
     * },
     * {
     * id: 35,
     * username: "发送",
     * create_time: "2018-05-02 19:21:50",
     * user_follow_id: null
     * }
     * ]*/
    public function friends($param)
    {

        $user_list = User::leftJoin("user_follow",function($join){
            $join->on('user_follow.follow_user_id','=',"user.id")->where("user_follow.user_id",'=',$this->user->id);
        })->leftJoin("user_follow as user_fans",function($join){
            $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
        })->whereRaw('fanwe_user_fans.id is not null')->whereRaw('fanwe_user_follow.id is not null')->select("user.id","user.username","user.avatar","user.create_time","user_follow.id as user_follow_id","user_fans.id as user_fans_id")->orderBy("user.id","asc")->paginate(15);

        //Helper::debug_sql;

        $user_list = $user_list->toArray()["data"];

        $this->setData("user_list",$user_list);
        return $this->success();
    }

    /**
     * @name myinvite
     * @description 我邀请的好友
     * @param 无，只需要授权有效即可
     * @return user_list: [
     * {
     * id: 34,
     * username: "12334的",
     * create_time: "2018-05-02 19:17:56",
     * user_follow_id: 6
     * },
     * {
     * id: 35,
     * username: "发送",
     * create_time: "2018-05-02 19:21:50",
     * user_follow_id: null
     * }
     * ]
     */
    public function myinvite($param)
    {
        $user_list = User::leftJoin("user_follow",function($join){
            $join->on('user_follow.follow_user_id','=',"user.id")->where("user_follow.user_id",'=',$this->user->id);
        })->leftJoin("user_follow as user_fans",function($join){
            $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
        })->where(['user.pid'=>$this->user->id])->select("user.id","user.username","user.avatar","user.create_time","user_follow.id as user_follow_id","user_fans.id as user_fans_id")->orderBy("user.id","asc")->paginate(15);

        //Helper::debug_sql;

        $user_list = $user_list->toArray()["data"];

        $this->setData("user_list",$user_list);
        return $this->success();
    }

    /**
     * @name myfans
     * @description 我的粉丝
     * @param 无，只需要授权有效即可
     * @return user_list: [
     * {
     * id: 5,
     * user_id: 63,
     * follow_user_id: 66,
     * create_time: "0000-00-00 00:00:00",
     * username: "123457",
     * user_follow_id: null
     * },
     * {
     * id: 4,
     * user_id: 65,
     * follow_user_id: 66,
     * create_time: "2018-05-07 19:04:10",
     * username: "讽德诵功",
     * user_follow_id: 3
     * }
     * ]
     */
    public function myfans($param)
    {

        $where = ["user_follow.follow_user_id"=>$this->user->id];

        $user_list = UserFollow::leftJoin("user","user.id","=","user_follow.user_id")

            ->leftJoin("user_follow as uf",function($join){
                $join->on('uf.follow_user_id','=',"user.id")->where("uf.user_id",'=',$this->user->id);
            })
            ->select("user.id","user.username","user.avatar","user.create_time","uf.id as user_follow_id","user_follow.id as user_fans_id")
            ->where($where)->orderBy("user_follow.id","desc")->paginate(10);

        $user_list = $user_list->toArray()["data"];

        //$total = UserFollow::where($where)->count();

        $this->setData("user_list",$user_list);
        return $this->success();
    }

    /**
     * @name myfollow
     * @description 我关注的
     * @param 无，只需要授权有效即可
     * @return user_list: [
     * {
     * id: 6,
     * user_id: 66,
     * follow_user_id: 34,
     * create_time: "0000-00-00 00:00:00",
     * username: "12334的"
     * user_follow_id
     * },
     * {
     * id: 3,
     * user_id: 66,
     * follow_user_id: 65,
     * create_time: "2018-05-07 19:04:10",
     * username: "讽德诵功"
     * user_follow_id
     * }
     * ]
     */
    public function myfollow($param)
    {

        $where = ["user_follow.user_id"=>$this->user->id];

        $user_list = UserFollow::leftJoin("user","user.id","=","user_follow.follow_user_id")
            ->leftJoin("user_follow as user_fans",function($join){
                $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
            })
            ->select("user.id","user.username","user.avatar","user.create_time","user_follow.id as user_follow_id","user_fans.id as user_fans_id")
            ->where($where)->orderBy("user_follow.id","desc")->paginate(10);

        $user_list = $user_list->toArray()["data"];

        //$total = UserFollow::where($where)->count();

        $this->setData("user_list",$user_list);

        return $this->success();
    }

    /**
     * @name follow
     * @description  关注 操作
     * @param
     * follow_user_id：被关注的用户id
     * @return array
     * 成功：[
     *  "user_follow_id" => 关系id,
     * ]
     * 失败：返回错误码及提示
     */
    public function follow($param)
    {

        $follow_user_id = intval($param['follow_user_id']);

        if ($follow_user_id > 0){
            if($follow_user_id==$this->user->id)
            {
                return $this->error("不能关注自己");
            }
            $user_follow = UserFollow::where(['user_id'=>$this->user->id,'follow_user_id'=>$follow_user_id])->first();

            if (!$user_follow){
                DB::beginTransaction();
                try{
                    $user_follow = new UserFollow();
                    $user_follow->user_id = $this->user->id;
                    $user_follow->create_time = date("Y-m-d H:i:s",time());
                    $user_follow->follow_user_id = $follow_user_id;
                    $user_follow->save();
                    DB::commit();
                    $this->setData("user_follow_id",$user_follow->id);
                    return $this->success("已关注");
                }
                catch (\Exception $e)
                {
                    DB::rollback();
                    return $this->error($e->getMessage());
                }
            }else{
                $this->setData("user_follow_id",$user_follow->id);
            }
            return $this->success();
        }else{
            return $this->error("用户不存在");
        }
    }



    /**
     * @name unfollow
     * @description  取消关注 操作
     * @param
     * follow_user_id：被关注的用户id
     * @return array
     * 成功：
     * 失败：返回错误码及提示
     */
    public function unfollow($param)
    {

        $follow_user_id = intval($param['follow_user_id']);
        if ($follow_user_id > 0) {
            $user_follow = UserFollow::where(['user_id' => $this->user->id, 'follow_user_id' => $follow_user_id])->delete();
            $this->setData("user_follow_id",0);
            return $this->success("已取消关注");
        }else{
            return $this->error("用户不存在");
        }
    }

    /**
     * @name recommend
     * @description 好友推荐列表(1、我邀请的用户 2、邀请我的人 3、邀请我的人 的 邀请用户)
     * @param 无，只需要授权有效即可
     * @return user_list: [
     * {
     * id: 34,
     * username: "12334的",
     * create_time: "2018-05-02 19:17:56",
     * user_follow_id
     * },
     * {
     * id: 35,
     * username: "发送",
     * create_time: "2018-05-02 19:21:50",
     * user_follow_id
     * }
     * ]
     */
    public function recommend($param)
    {

        $pid = intval($this->user->pid);
        $where = "(fanwe_user.pid in (".$this->user->id.",".$pid.") or fanwe_user.id = ".$pid.") and fanwe_user.id <> ".$this->user->id." and  fanwe_user_follow.id is null";

        $user_list = User::leftJoin("user_follow",function($join){
            $join->on('user_follow.follow_user_id','=',"user.id")->where("user_follow.user_id",'=',$this->user->id);
        })->leftJoin("user_follow as user_fans",function($join){
            $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
        })->whereRaw($where)->select("user.id","user.username","user.create_time","user.avatar","user_follow.id as user_follow_id","user_fans.id as user_fans_id")->orderBy("user.id","asc")->paginate(10);

        //Helper::debug_sql;

        $user_list = $user_list->toArray()["data"];

        $this->setData("user_list",$user_list);
        return $this->success();
    }


    /**
     * @name myinvite
     * @description 我邀请的好友
     * @param
     * mobile：手机号
     * @return user_list: [
     * {
     * id: 34,
     * username: "12334的",
     * create_time: "2018-05-02 19:17:56",
     * user_follow_id: 6
     * },
     * {
     * id: 35,
     * username: "发送",
     * create_time: "2018-05-02 19:21:50",
     * user_follow_id: null
     * }
     * ]
     */
    public function find($param)
    {

        $mobile = trim($param['search_key']);
        if (strlen($mobile) == 11) {
            $user_list = User::leftJoin("user_follow", function ($join) {
                $join->on('user_follow.follow_user_id', '=', "user.id")->where("user_follow.user_id", '=', $this->user->id);
            })->leftJoin("user_follow as user_fans",function($join){
                $join->on('user_fans.user_id','=',"user.id")->where("user_fans.follow_user_id",'=',$this->user->id);
            })->where(['user.mobile' => $mobile])->select("user.id", "user.username","user.avatar", "user.create_time", "user_follow.id as user_follow_id", "user_fans.id as user_fans_id")->orderBy("user.id", "asc")->paginate(15);

            //Helper::debug_sql;

            $user_list = $user_list->toArray()["data"];

            $this->setData("user_list", $user_list);
            return $this->success();
        }else{
            return $this->error("不是有效的手机号码".$mobile);
        }

    }

    /**
     * @name apply_friend
     * @description  申请添加好友 操作
     * @param
     * friend_user_id：被添加的用户id
     * @return array
     */
    /*
    public function apply_friend($param)
    {

        $friend_user_id = intval($param['friend_user_id']);

        if ($friend_user_id > 0){
            $user_friend = UserFriend::where(['user_id'=>$this->user->id,'friend_user_id'=>$friend_user_id])->first();

            if (!$user_friend){
                $user_friend_apply = UserFriendApply::where(['user_id'=>$this->user->id,'friend_user_id'=>$friend_user_id])->first();
                if (!$user_friend_apply) {
                    DB::beginTransaction();
                    try {
                        $user_friend_apply = new UserFriendApply();
                        $user_friend_apply->user_id = $this->user->id;
                        $user_friend_apply->create_time = date("Y-m-d H:i:s", time());
                        $user_friend_apply->friend_user_id = $friend_user_id;
                        $user_friend_apply->status = 0;//状态:0待确认;1:已添加;2:拒绝;3:已过期
                        $user_friend_apply->save();
                        DB::commit();

                        //插入一条短信通知

                        $this->setData("user_friend_apply", $user_friend_apply->id);
                        return $this->success();
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $this->error($e->getMessage());
                    }
                }else{
                   if ($user_friend_apply['status'] == 0){
                       $this->setData("user_friend_apply_id", $user_friend_apply->id);
                       return $this->success("已申请,等待对方确认");
                   }else {
                       return $this->success("已经发过好友申请");
                   }
                }
            }else{
                return $this->error("已经是好友");
            }
        }else{
            return $this->error("用户不存在");
        }
    }
    */
}