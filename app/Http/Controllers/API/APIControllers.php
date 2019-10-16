<?php


namespace App\Http\Controllers\API;

use App\BlogTag;
use App\Http\Controllers\Controller;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Illuminate\Http\Request;

class APIControllers extends Controller
{
    public function test(){

        return response()->json(['first'=>'我爱你','second'=>123,'end'=>'I Love You'])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
    //获取博客类型
    public function GetBlogTag(){
        $blogtag = BlogTag::all();
        //dd($blogtag);
        return json_encode([$blogtag,'msg'=>'注册错误'],JSON_UNESCAPED_UNICODE);

    }
    public function wyp(Request $request){
        return $request->info;
    }

    //测试分词
    public function fenci(Request $request){
        ini_set('memory_limit','1024M');
        Jieba::init();
        Finalseg::init();
        $set_list =Jieba::cut($request->info);
        return json_encode(['原句'=>$request->info,'分词结果'=>$set_list],JSON_UNESCAPED_UNICODE);
    }




}
