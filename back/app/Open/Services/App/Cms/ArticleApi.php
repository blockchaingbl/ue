<?php
namespace App\Open\Services\App\Cms;

use App\Helper;
use App\Http\Models\Article;
use App\Http\Models\ArticleCp;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;


class ArticleApi extends FanweAuthService
{

    /**
     * @name index
     * @description  文章列表
     * @param
     * page 页数
     * @return [
     *  'articles'=>[]
     * ]
     */
    public function index()
    {
        $lists = Article::select('title','id','image','create_time')->where('publish',1)->orderBy('sort','asc')->orderBy('id','desc')->paginate(20)->toArray();
        $lists = $lists['data'];
        $this->setData('articles',$lists);
        return $this->success();
    }

    /**
     * @name detail
     * @description  文章详情
     * @param
     * id 文章id
     * @return [
     *  'article'
     * ]
     */
    public function detail($param)
    {
        $user = $this->user;
        $id = $param['id'];
        $article = Article::where('id',$id)->where('publish',1)->first();
        $return_cp = Helper::trimNumber(ArticleCp::cp_return($article,$user),8);
        $this->setData('article',$article);
        $this->setData('return_cp',$return_cp);
        return $this->success();
    }

    /**
     * @name cp_return
     * @description  看资讯增加算力
     * @param
     * id 文章id
     * @return [
     *  'return_cp' 增加算力
     * ]
     */
    public function cp_return($param){
        $user = $this->user;
        $article = Article::find($param['id']);
        if(!$article){
            return $this->error('无此记录');
        }
        $return_cp = Helper::trimNumber(ArticleCp::cp_return($article,$user),8);
        $this->setData('return_cp',$return_cp);
        return $this->success();
    }
}