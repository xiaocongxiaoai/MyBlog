<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ImgController extends Controller
{
    //处理文件上传、图片上传//blog图片上传
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

    //处理blog封面图片上传
    public function BlogImgUp(Request $request){
        $result = $this->img($request,"blog");
        return json_encode(['msg_code'=>$result[0],'data'=> $result[1]],JSON_UNESCAPED_UNICODE);
    }

    //处理用户头像图片上传
    public function UserhImgUp(Request $request){
        $result = $this->img($request,"userh");
        return json_encode(['msg_code'=>$result[0],'data'=> $result[1]],JSON_UNESCAPED_UNICODE);
    }

    //处理用户背景图片上传
    public function UserbImgUp(Request $request){
        $result = $this->img($request,"userb");
        return json_encode(['msg_code'=>$result[0],'data'=> $result[1]],JSON_UNESCAPED_UNICODE);
    }

    //封装img函数
    public function img($request,$type){
        switch ($type)
        {
            case "blog":
                $types = 1;
                break;
            case "userh":
                $types = 0;
                break;
            case "userb":
                $types = 2;
                break;
            default:
                $types = 3;   //垃圾类型数据
        }

        $photos = $request->file('file');

        $entension = $photos->getClientOriginalExtension();
        if(!in_array($entension,['jpg','png'])){
            return ['msg_code'=>1,'data'=>'文件格式不正确！'];
        }
        if(round($photos->getSize()/1048576,2)>10){
            return ['msg_code'=>1,'data'=>'文件大小不对！'];
        }
        //无论单个多个图片都以多个图片形式传递
        //获取图片名字及创建存储名
        $filename =Uuid::uuid1()."-".$photos->getClientOriginalName();
        $imgpath = '../public/img/'.Auth::guard('api')->user()->name.'/'.$type;
        $Path = file_exists($imgpath);

        //如果不存在img文件夹层就创建
        if(!$Path){
            mkdir($imgpath);
        }
//      更具前端传输方式选择是否批量处理
//        foreach ($photos as $photo){
//            $filename =Uuid::uuid1()."-".$photo->getClientOriginalName();
//            array_push($filenames,$filename);
//            //保存图片
//            $photo->move($Path, $filename);
//        }
        //保存之前删除原有的图片文件

        $photos->move($imgpath, $filename);
        $filepath = "xiecongcong.test:8000/img/".Auth::guard('api')->user()->name."/".$type."/".$filename;
        $Imginfo = new Img();
        $Imginfo->ImgOnlyId = Uuid::uuid1();
        $Imginfo->ImgUrl = $filepath;
        $Imginfo->user_id = Auth::guard('api')->user()->userOnlyId;
        $Imginfo->ImgType = $types;
        if($imgpath->save()){
            return ['msg_code'=>0,'data'=>'上传成功！'];
        }else{
            return ['msg_code'=>1,'data'=>'上传失败！'];
        }
    }

    public function DeleteImg($types){
        $ImgUrl = Img::where('ImgType','=',$types)
            ->where('user_id','=',Auth::guard('api')->user()->userOnyId)
            ->OrderBy('created_at','desc')
            ->first();
        if(!empty($ImgUrl)){
            //拼接图片url信息
            $url = '../public/img/';
            $url2 = ltrim($ImgUrl->ImgUrl,"xiecongcong.test:8000/img/");
            $url = $url.$url2;
            $result = unlink($url);
        }
    }
}
