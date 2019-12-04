<?php

namespace App\Http\Controllers\API;

use App\BlogInfo;
use App\UserAction;
use DemeterChain\B;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    //查询用户信息
    //查看自己的信息
    //修改自己的信息
    //查看他人的信息
    //点赞微博
    //点赞/点踩评论
    //查看自己历史查看记录
    public function History(){
        $history = UserAction::where('userId','=',Auth::guard('api')->user()->userOnlyId)
        ->join('t_blog_info','t_user_action.blogInfoId','t_blog_info.blogOnlyId')
        ->get(['t_blog_info.*','t_user_action.created_at']);
        return json_encode(['msg_code'=>0,'data'=>$history],JSON_UNESCAPED_UNICODE);
    }

    //自己blog的管理(增删改)
    //1.发表blog
    public function createBlog(Request $request){
        //验证blog标题
        //api_token 访问用户信息语法  guard(api)是指这个Auth查询类别是指config/auth.php 中guards走api验证
        //dd(Auth::guard('api')->user()->name);
        if(is_null($request->title)){
            $msg_code = 1;    //返回错误信息
            $msg[]="标题不能为空！";
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }else{
            if(strpos($request->title,'>')-strpos($request->title,'<')>0){
                $msg_code = 1;    //返回错误信息
                $msg[]="标题含有非法字符！";
                return json_encode(['msg_code' => $msg_code , 'msg' => $msg],JSON_UNESCAPED_UNICODE);
            }
            if(mb_strlen($request->title)>35||mb_strlen($request->title)<3){
                $msg_code = 1;    //返回错误信息
                $msg[]="标题不得少于三个字，不得多于35个字！";
                return json_encode(['msg_code' => $msg_code , 'msg' => $msg],JSON_UNESCAPED_UNICODE);
            }
        }
        //验证blog内容

        if (is_null($request->blogContent)){
            $msg_codes = 1;    //返回错误信息
            $msg[] = "内容不能为空！";
            return json_encode(['msg_code' => $msg_codes, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
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
        $bloginfo->blogUserTypeId = is_null($request->blogUserType)?"":$request->blogUserType;
        $bloginfo->blogTag = is_null($request->blogTag)?"无":$request->blogTag;
        $bloginfo->user_id = Auth::guard('api')->user()->userOnlyId;
        $bloginfo->isPublic = is_null($request->isPublic)?1:$request->isPublic;        //是否公开,默认值为1 代表是
        //是否可疑（为标题存在敏感内容）
        $bloginfo->isSuspicious = 0;               //默认值为0 代表不是含有侮辱性的
        $bloginfo->reportNum = 0;                  //举报人数默认值为0
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

    //2.查询自己的blog(正常blog，异常blog)
    //
    public function MyBlog(Request $request){
        //当前页
        $index = intval(is_null($request->index)?1:$request->index);
        //每页展示数
        $pagecount =intval(is_null($request->pagecount)?30:$request->pagecount);
        //展示blog类别
        $blogtypeId = is_null($request->blogtypeId)?"":$request->blogtypeId;
        //默认以某类别进行展示,且默认按时间先后进行展示
        $type = intval(is_null($request->type)?1:$request->type);    //表示是否点击按时间先后还是热度，如果没有点击默认选择时间先后 1：时间先后、2：热度先后
        if($type==1){
            $types = "created_at";
        }else{
            //热度先后展示(点赞人数)
            $types = "likeNum";
        }
        //我的博客的好与坏
        $isGood = intval(is_null($request->isGood)?0:$request->isGood);           //1：代表有异常的blog  0：代表正常的blog
        //blog总数
        $blogNum = BlogInfo::
            where('isPublic','=',1)
            ->where('isSuspicious','=',$isGood)
            ->where('user_id','=',Auth::guard('api')->user()->userOnlyId)
            ->where('blogOnlyId','like','%'.$blogtypeId.'%')
            ->count();

        //blog分页数据
        $current = ($index-1)*$pagecount;    //开始取数据的位置
        $goodinfo = BlogInfo::
            where('user_id','=',Auth::guard('api')->user()->userOnlyId)
            ->where('blogTypeId','like','%'.$blogtypeId.'%')
            ->where('isSuspicious','=',$isGood)
            ->orderBy($types,'desc')
            ->offset($current)->limit($pagecount-1)
            ->get();
        return json_encode(['blogNum'=>$blogNum,'data'=>$goodinfo],JSON_UNESCAPED_UNICODE);
    }

    //更改我写的blog
    public function BlogChange(Request $request){
        //验证blog是否存在
        $bloginfo = BlogInfo::where('blogOnlyId','=',$request->blogId)->first();
        if(is_null($bloginfo)){
            return json_encode(['msg_code'=>1,'msg'=>'该博客已被删除或不存在！'],JSON_UNESCAPED_UNICODE);
        }
        //验证是否属于用户本身
        if($bloginfo->user_id != Auth::guard('api')->user()->userOnlyId){
            return json_encode(['msg_code'=>1,'msg'=>'你无法更改别人的博客！'],JSON_UNESCAPED_UNICODE);
        }
        //验证blog是否处于异常状态
        switch ($bloginfo->isSuspicious){
            case 1 :
                return json_encode(['msg_code'=>1,'msg'=>'该blog存在问题暂时无法更改详情请看审核反馈'],JSON_UNESCAPED_UNICODE);
                break;
            case 2 :
                return json_encode(['msg_code'=>1,'msg'=>'该blog可能存在问题，请等待管理员审核'],JSON_UNESCAPED_UNICODE);
                break;
            case 3 :
                return json_encode(['msg_code'=>1,'msg'=>'该blog被多名用户举报，请等待管理员审核'],JSON_UNESCAPED_UNICODE);
                break;
            default :
                break;
        }

        //更改blog
        $bloginfo->blogTitle = $request->title;
        $bloginfo->blogContent = $request->blogContent;
        $bloginfo->blogTypeId = $request->blogType;
        $bloginfo->blogUserTypeId = is_null($request->blogUserType)?"":$request->blogUserType;
        $bloginfo->blogTag = is_null($request->blogTag)?"无":$request->blogUserType;
        $bloginfo->isPublic = is_null($request->isPublic)?1:$request->isPublic;
        if($bloginfo->save()){
            return json_encode(['msg_code'=>0,'msg'=>'更新成功！']);
        }else{
            return json_encode(['msg_code'=>1,'msg'=>'出现未知错误请联系管理员！'],JSON_UNESCAPED_UNICODE);
        }
    }
    //我的博客标题智能返回
    public function MyBlogTitle(Request $request){
        //$title = BlogInfo::where('blogTitle','like','%'.$request->title.'%')->take(8)->get();
        $title = DB::table('t_blog_info')
            ->where('blogTitle','like','%'.$request->title.'%')
            ->where('user_id','=',Auth::guard('api')->user()->userOnlyId)
            ->select('blogTitle')
            ->get();
        return json_encode(['msg'=>0,'data'=>$title],JSON_UNESCAPED_UNICODE);
    }


}
