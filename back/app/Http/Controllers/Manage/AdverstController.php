<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\Adversts;
use App\Http\Models\AdverstsCategory;
use App\Http\Models\Article;
use App\Http\Models\CoinType;
use App\Http\Models\Manage\Admin;
use App\Http\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdverstController extends AuthBaseController
{

    //广告管理
    public function index(Request $request)
    {
        $lists = AdverstsCategory::paginate(20);
        return view("manage.adverst.index",['lists'=>$lists]);
    }

    public function adversts($id,Request $request)
    {
        $param = $request->all();
        $cate = AdverstsCategory::find($id);
        $lists = Adversts::where(function ($query)use($id,$param){
            $query->where('cate_id',$id);
            if(is_numeric($param['open'])){
                $query->where('open',$param['open']);
            }
            if($param['keyword']){
                $query->where('name','like',"%{$param['keyword']}%");
            }
        })->paginate(20);

        return view("manage.adverst.adversts",['lists'=>$lists,'cate'=>$cate,'param'=>$param]);
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

        $fileFolder = "adversts_image";
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
        if(!$param['name'])
        {
            return $this->error('请填写标题');
        }
        if(!$param['url'])
        {
            return $this->error('请编辑文章内容');
        }
        $adverst = Adversts::find($param['id']);
        if(!$adverst)
        {
            $adverst = new Adversts();
            $adverst->create_time = date('Y-m-d H:i:s');
        }
        $adverst->cate_id = $param['cate_id'];
        $adverst->name = $param['name'];
        $adverst->image = $param['image'];
        $adverst->app_open = $param['app_open'];
        $adverst->url = $param['url'];
        $adverst->open = $param['open'];
        $result = $adverst->save();
        if($result)
        {
            return $this->success();
        }else{
            return $this->error();
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $re = Adversts::destroy($id);
        if($re)
        {
            return $this->success();
        }
        else{
            return $this->error();
        }
    }

    public function cate_save(Request $request)
    {
        $param = $request->all();
        $id = $param['id'];
        $cate = AdverstsCategory::find($id);
        if(!$id)
        {
            $cate = new AdverstsCategory();
        }
        $cate->name = $param['name'];
        $cate->save();
        return $this->success();
    }



}
