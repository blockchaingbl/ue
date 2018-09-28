<?php
namespace App\Open\Services\App\Apply;


use App\FanweErrcode;
use App\Http\Models\Apply;
use App\Http\Models\ApplyNode;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;



class ApplyApi extends FanweAuthService
{

    /**
     * @name apply
     * @description 申请成为讲师
     * @param
     * apply_id id
     * @return array
     */
    public function apply($param)
    {
        $user = $this->user;
        if(!$param['name'])
        {
            return $this->error('请填写姓名');
        }
        if(!$param['mobile'])
        {
            return $this->error('请填写手机号');
        }
        if(!$param['area'])
        {
            return $this->error('请填写市县名称');
        }
        if(!$param['industry'])
        {
            return $this->error('请填写现在从事行业');
        }
        if(!is_numeric($param['team_number']))
        {
            return $this->error('请填写现团队人数');
        }
        if(!is_numeric($param['team_number_expect']))
        {
            return $this->error('请填写预计发展规模');
        }
        $need_cp = db_config('APPLY_CP_SOCIETY');
        if($user->cp_total<$need_cp)
        {
            return $this->error("您的算力不够{$need_cp}，不符合申请条件",FanweErrcode::CP_NO_ENOUGH);
        }
        $apply = Apply::where('user_id',$user->id)->first();
        if($apply&&$apply->status==0)
        {
            return $this->error('正在申请中,请耐心等待');
        }
        $pid = 0;
        $pid_mobile = '';
        if($param['pid_mobile'] && !$user->pid)
        {
            $parent = User::where('mobile',$param['pid_mobile'])->first();
            if(!$parent)
            {
                return $this->error('挂靠社群不存在');
            }else{
                $pid = $parent->id;
                $pid_mobile = $param['pid_mobile'];
            }
        }
        if(!$apply)
        {
            $apply = new Apply();
            $apply->create_time = date('Y-m-d H:i:s');
        }

        $apply->status = 0;
        $apply->area = $param['area'];
        $apply->industry = $param['industry'];
        $apply->team_number = $param['team_number'];
        $apply->user_id = $user->id;
        $apply->team_number_expect = $param['team_number_expect'];
        $apply->name = $param['name'];
        $apply->mobile = $param['mobile'];
        $apply->finish_month = $param['finish_month'];
        $apply->update_time = date('Y-m-d H:i:s');
        $apply->source = $param['source'];
        $apply->full_time = $param['full_time'];
        $r = $apply->save();
        if($r)
        {
            return $this->success();
        }else{
            return $this->error();
        }
    }

    /**
     * @name apply
     * @description 申请成为讲师
     * @param
     * apply_id id
     * @return array
     */
    public function applynode($param)
    {
        $user = $this->user;
        if(!$param['name'])
        {
            return $this->error('请填写姓名');
        }
        if(!$param['mobile'])
        {
            return $this->error('请填写手机号');
        }
        if(!$param['area'])
        {
            return $this->error('请填写市县名称');
        }
        if(!$param['industry'])
        {
            return $this->error('请填写现在从事行业');
        }
        if(!is_numeric($param['team_number']))
        {
            return $this->error('请填写现月营业额');
        }
        if(!is_numeric($param['team_number_expect']))
        {
            return $this->error('请填写加入平台预期月营业额');
        }
        $need_cp = db_config('APPLY_CP_NODE');
        if($user->cp_total<$need_cp)
        {
            return $this->error("您的算力不够{$need_cp}，不符合申请条件",FanweErrcode::CP_NO_ENOUGH);
        }
        $apply = ApplyNode::where('user_id',$user->id)->first();
        if($apply&&$apply->status==0)
        {
            return $this->error('正在申请中,请耐心等待');
        }
        if(!$apply)
        {
            $apply = new ApplyNode();
            $apply->create_time = date('Y-m-d H:i:s');
        }

        $apply->status = 0;
        $apply->area = $param['area'];
        $apply->industry = $param['industry'];
        $apply->team_number = $param['team_number'];
        $apply->user_id = $user->id;
        $apply->team_number_expect = $param['team_number_expect'];
        $apply->name = $param['name'];
        $apply->mobile = $param['mobile'];
        $apply->update_time = date('Y-m-d H:i:s');
        $apply->full_time = $param['full_time'];
        $apply->industry_type = $param['industry_type'];
        $r = $apply->save();
        if($r)
        {
            return $this->success();
        }else{
            return $this->error();
        }
    }

    /**
     * @name applyinfo
     * @description 申请信息接口
     * @param
     * @return array
     *  status 0 申请中 1申请通过 2申请不通过
     */
    public function applyinfo()
    {
        $user = $this->user;
        $info = Apply::where('user_id',$user->id)->first();
        if($info)
        {
            $this->setData('pid',$user->pid);
            $this->setData('info',$info);
            return $this->success();
        }else{
            $this->setData('pid',$user->pid);
            return $this->error('未申请',FanweErrcode::NO_APPLY);
        }
    }

    /**
     * @name applyinfo
     * @description 申请信息接口
     * @param
     * @return array
     *  status 0 申请中 1申请通过 2申请不通过
     */
    public function applynodeinfo()
    {
        $user = $this->user;
        $info = ApplyNode::where('user_id',$user->id)->first();
        if($info)
        {
            $this->setData('pid',$user->pid);
            $this->setData('info',$info);
            return $this->success();
        }else{
            $this->setData('pid',$user->pid);
            return $this->error('未申请',FanweErrcode::NO_APPLY);
        }
    }

    public function attached($param){
        $user = $this->user;
        if($user->attached)
        {
            return $this->error('已有社群');
        }
        if(!$param['pid_mobile'])
        {
            return $this->error('请填写手机号');
        }
        $parent =  User::where('mobile',$param['pid_mobile'])->first();
        if($parent->id==$user->id)
        {
            return $this->error('无法挂靠自己');
        }
        if(!$parent)
        {
            return $this->error('社群不存在');
        }
        $apply = Apply::where(['user_id'=>$parent->id])->first();
        if(!$apply)
        {
            return $this->error('社群不存在');
        }
        if($apply->status!=1)
        {
            return $this->error('该社群还未通过审核，挂靠失败');
        }
        $result = Apply::checkTen($user);
        if($result)
        {
            return $this->error('已有挂靠');
        }
        $user->attached = $parent->id;
        $user->save();
        return $this->success('加入成功');
    }
}