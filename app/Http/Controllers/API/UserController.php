<?php

namespace App\Http\Controllers\API;

use App\BlogInfo;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


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
        //api_token 访问用户信息语法  guard(api)是指这个Auth查询类别是指config/auth.php 中guards走api验证
        //dd(Auth::guard('api')->user()->name);
        if(is_null($request->blogTitle)){
            $msg_code = 1;    //返回错误信息
            $msg[]="标题不能为空！";
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }else{
            if(strpos($request->title,'>')-strpos($request->title,'<')>0){
                $msg_code = 1;    //返回错误信息
                $msg[]="标题含有非法字符！";
            }
            if(strlen($request->title)>70||strlen($request->title)<6){
                $msg_code = 1;    //返回错误信息
                $msg[]="标题不得少于三个字，不得多于35个字！";
            }
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //验证blog内容
        if (is_null($request->blogContent)){
            $msg_code = 1;    //返回错误信息
            $msg[] = "内容不能为空！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }

        //验证blog类型是否选择   暂定单选，单类型
        if (is_null($request->blogType)){
            $msg_code = 1;    //返回错误信息
            $msg[] = "类型需要！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }
        //验证blog的提交者是否为空
        if(is_null(Auth::guard('api')->user())){
            $msg_code = 1;    //返回错误信息
            $msg[] = "系统错误请通知管理员！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }
        //api_token验证
        if(is_null($request->api_token)){
            $msg_code = 1;    //返回错误信息
            $msg[] = "请求错误请检查token！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }
        //全部验证通过后新增博客
        $bloginfo = new BlogInfo;
        $bloginfo->blogOnlyId =\Ramsey\Uuid\Uuid::uuid1();
        $bloginfo->blogTitle = $request->title;
        $bloginfo->blogContent = $request->blogContent;
        $bloginfo->blogTypeId = $request->blogType;
        $bloginfo->blogUserTypeId = is_null($request->blogUserType)?null:$request->blogUserType;
        $bloginfo->blogTag = is_null($request->blogTag)?"无":$request->blogUserType;
        $bloginfo->user_id = Auth::guard('api')->user()->userOnlyId;
        $bloginfo->isPublic = $request->isPublic;        //是否公开,默认值为1 代表是
        //是否可疑（为标题存在敏感内容）
        $bloginfo->isSuspicious = 0;               //默认值为0 代表不是含有侮辱性的

        if($bloginfo->save()){
            $msg_code = 0;    //返回正确信息
            $msg[] = "提交成功！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }else{
            $msg_code = 1;    //返回错误信息
            $msg[] = "提交失败，请联系管理员！";
            return json_encode(['msg_code' => $msg_code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        }

    }

}
