<?php
namespace App\Open\Services\App\Cms;


use App\Http\Models\Adversts;
use App\Open\Services\FanweAuthService;



class AdverstApi extends FanweAuthService
{

    /**
     * @name article
     * @description  文章列表
     * @param
     * page 页数
     * @return [
     *  'adversts'=>[]
     * ]
     */
    public function article()
    {
        $lists = Adversts::where('open',1)->where('cate_id',config('app.adverst.article'))->orderBy('id','desc')->paginate(20)->toArray();
        $lists = $lists['data'];
        $this->setData('adversts',$lists);
        return $this->success();
    }

}