<?php

namespace App\Http\Controllers\Web;





use App\Http\Models\Article;

class ArticleController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function detail($id)
    {
        $article = Article::where(['id'=>$id,'publish'=>'1'])->first();
        if(!$article){
            abort(404);
        }
        return view('web.article.detail',['article'=>$article]);
    }



}
