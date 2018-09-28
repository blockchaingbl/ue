<?php

namespace App\Http\Controllers\Manage;
use App\Helper;
use App\Http\Models\Article;

use Illuminate\Http\Request;


class ArticleController extends AuthBaseController
{

    //文章管理
    public function index(Request $request)
    {
        $param = $request->all();
        $lists = Article::where(function ($query)use($param){
            if($param['keyword']){
                $query->where('title','like',"%{$param['keyword']}%");
            }
            if($param['date']){
                $dateStr = explode('~',$param['date']);
                $begin_date = $dateStr[0];
                $end_date = $dateStr[1];
                if($begin_date){
                    $query->where('create_time','>=',"$begin_date 00:00:00");
                }
                if($end_date){
                    $query->where('create_time','<=',"$end_date 23:59:59");
                }
            }
            if(is_numeric($param['publish_status']))
            {
                $query->where('publish','=',$param['publish_status']);
            }
        })->paginate(20);

        return view("manage.article.index",['lists'=>$lists,'param'=>$param]);
    }

    public function upload_image(Request $request){
        $upload_file = $request->file("file");

        //验证最大上传大小限制
        $maxSize = config('app.file_maxsize')*1024*1024;

        if($upload_file->getSize() >= $maxSize) {
            $return['errcode'] = 10000;
            $return['message'] = '文件大小超出最大限制';
            return $return;
        }

        $fileFolder = "article_image";
        $fileName = time().".".$upload_file->getClientOriginalExtension();
        $file = [
            'fileFolder'=>$fileFolder, // 上传到OSS的指定文件夹
            'fileName'=>$fileName, // 上传文件名（xxx.jpg）
            'filePath'=>$upload_file->getPath()."/".$upload_file->getFilename() // 上传文件对象
        ];
        $res = Helper::upload_to_oss($file);

        if($res){
            $return['errcode'] = 0;
            $return['message'] = '上传成功';
            $return['data'] = ['src'=>$res['src'],"width"=>$res['width'],'height'=>$res['height']];
            return $return;
        }
        else{
            $return['errcode'] = 10000;
            $return['message'] = '上传失败';
            return $return;
        }
    }

    public function save(Request $request)
    {

        $param = $request->all();
        if(!$param['image'])
        {
            return $this->error('请上传图片');
        }
        if(!$param['title'])
        {
            return $this->error('请填写标题');
        }
        if(!$param['content'])
        {
            return $this->error('请编辑文章内容');
        }
        if($param['cp_return'] && $param['cp_return_num'] <=0){
            return $this->error('请输入返算力值');
        }

        $article = Article::find($param['id']);
        if(!$article)
        {
            $article = new Article();
            $article->create_time = date('Y-m-d H:i:s');
        }
        $article->title = $param['title'];
        $article->image = $param['image'];
        $article->content = $param['content'];
        $article->publish = $param['publish'];
        $article->cp_return = $param['cp_return'];
        $article->cp_return_num = $param['cp_return_num'];
        $article->sort= $param['sort'];
        $result = $article->save();
        if($result)
        {
            return $this->success();
        }else{
            return $this->error();
        }
    }

    public function detail(Request $request)
    {
        $id = $request->input('id');
        if(!$id)
        {
            return $this->error('参数错误');
        }
        $article = Article::find($id);
        if(!$article){
            return $this->error();
        }
        return ['article'=>$article,'errcode'=>0];
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $re = Article::destroy($id);
        if($re)
        {
            return $this->success();
        }
        else{
            return $this->error();
        }
    }



}
