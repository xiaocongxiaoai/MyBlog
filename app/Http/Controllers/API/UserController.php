<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //查询用户信息
    //查看自己的信息
    //修改自己的信息
    //查看他人的信息
    //自己blog的管理(增删改)
    //1.发表blog
    public function createBlog(Request $request){
        //验证blog标题
        if(is_null($request->blogTitle)){
            $msg_code = 0;    //返回错误信息
            $msg[]="标题不能为空！";
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }else{
            if(strpos($request->title,'>')-strpos($request->title,'<')>0){
                $msg_code = 0;    //返回错误信息
                $msg[]="标题含有非法字符！";
            }
            if(strlen($request->title)>70||strlen($request->title)<6){
                $msg_code = 0;    //返回错误信息
                $msg[]="标题不得少于三个字，不得多于35个字！";
            }
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //验证blog内容
        if (is_null($request->blogContent)){
            $msg_code = 0;    //返回错误信息
            $msg[] = "内容不能为空！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }

        //验证blog类型是否选择   暂定单选，单类型
        if (is_null($request->blogType)){
            $msg_code = 0;    //返回错误信息
            $msg[] = "类型需要选择！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }
        //验证blog的提交者是否为空
        if(is_null($request->userId)){
            $msg_code = 0;    //返回错误信息
            $msg[] = "系统错误请通知管理员！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }
        //api_token验证
        if(is_null($request->apitoken)){
            $msg_code = 0;    //返回错误信息
            $msg[] = "请求错误请检查token！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }
        //全部验证通过后新增博客




    }

}
