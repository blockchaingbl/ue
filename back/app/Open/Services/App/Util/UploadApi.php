<?php
namespace App\Open\Services\App\Util;

use App\FanweErrcode;
use App\Helper;
use App\Http\Models\Web\Base;
use App\Http\Models\Web\User;
use App\Open\Services\FanweAuthService;
use Illuminate\Support\Facades\DB;


class UploadApi extends FanweAuthService
{

    /**
     * @name index
     * @description  文件上传
     * @param
     * type：上传文件类型（avatar：头像；alipay：支付宝二维码；weixin：微信二维码）
     * file：文件对象
     * @return
     * 成功：返回file_url文件地址
     * 失败：返回错误码及提示
     */
    public function index($param)
    {
        $user = $this->user;
        $type = $param["type"];
        $upload_file = $param["file"];

        if(!$type) {
            return $this->error("请输入上传文件类型",FanweErrcode::UPLOAD_TYPE_NOT_EXSITS);
        }

        //验证最大上传大小限制
        $maxSize = config('app.file_maxsize')*1024*1024;
        if($upload_file->getSize() >= $maxSize) {
            return $this->error("文件大小超出最大限制",FanweErrcode::UPLOAD_MAX_LIMIT);
        }

        $fileFolder = "upload/".$type."/".$user->id;
        $fileName = time().".".$upload_file->getClientOriginalExtension();

        $file = [
            'fileFolder'=>$fileFolder, // 上传到OSS的指定文件夹
            'fileName'=>$fileName, // 上传文件名（xxx.jpg）
            'filePath'=>$upload_file->getPath()."/".$upload_file->getFilename() // 上传文件对象
        ];
        $res = Helper::upload_to_oss($file);

        if($res){

            if($type=="avatar")
            {
                User::where("id",$user->id)->update(["avatar"=>$res["src"]]);
            }
            $this->setData("file_url",$res["src"]);
            return $this->success("上传成功");
        }
        else{
            return $this->error("上传失败",FanweErrcode::UPLOAD_FILE_FAIL);
        }
    }

}