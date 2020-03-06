<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class ImgController extends Controller
{
    //处理文件上传、图片上传
    public function ImgUp(Request $request){

        $photos = $request->file('file');

        $entension = $photos->getClientOriginalExtension();
        if(!in_array($entension,['jpg','png'])){
            return ['msg_code'=>1,'msg'=>'文件格式不正确！'];
        }
        if(round($photos->getSize()/1048576,2)>10){
            return ['msg_code'=>1,'msg'=>'文件大小不对！'];
        }
        //无论单个多个图片都以多个图片形式传递
        //获取图片名字及创建存储名
        $filename =Uuid::uuid1()."-".$photos->getClientOriginalName();
            $Path = file_exists('../public/img');

        //如果不存在img文件夹层就创建
        if(!$Path){
            mkdir('../public/img');
        }
        //更具前端传输方式选择是否批量处理
//        foreach ($photos as $photo){
//            $filename =Uuid::uuid1()."-".$photo->getClientOriginalName();
//            array_push($filenames,$filename);
//            //保存图片
//            $photo->move($Path, $filename);
//        }
        $photos->move('../public/img', $filename);
        $filepath = "xiecongcong.test:8000/img/".$filename;
        return json_encode(['msg_code'=>0,'path'=> $filepath],JSON_UNESCAPED_UNICODE);
    }

}
