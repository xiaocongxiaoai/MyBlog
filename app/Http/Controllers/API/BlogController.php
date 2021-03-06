<?php

namespace App\Http\Controllers\API;

use App\BlogInfo;
use App\BlogTag;
use App\BlogType;
use App\Comment;
use App\uBlogType;
use App\User;
use App\UserAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class BlogController extends Controller
{
    //查询blog/展示
    public function Search(Request $request){
        //判断申请类型 0 为全blog查询 1为查询他人blog

        $search = is_null($request->search)?"":$request->search;
        //当前页
        $index = intval(is_null($request->index)?1:$request->index);
        //每页展示数
        $pagecount =intval(is_null($request->pagecount)?30:$request->pagecount);
        //展示blog类别
        $blogtypeId = is_null($request->blogtypeId)?"":$request->blogtypeId;
        //默认以某类别进行展示,且默认按时间先后进行展示
        $type = intval(is_null($request->type)?1:$request->type);    //表示是否点击按时间先后还是热度，如果没有点击默认选择时间先后 1：时间先后、2：热度先后
        if($request->type==1){
            $types = "created_at";
        }else{
            //热度先后展示(点赞人数)
            $types = "likeNum";
        }
        //blog总数
        $blogNum = BlogInfo::where('blogTitle','like','%'.$search.'%')
            ->where('isPublic','=',1)
            ->where('isSuspicious','=',0)
            ->count();

        //blog分页数据
        $current = ($index-1)*$pagecount;    //开始取数据的位置

        $bloginfo = BlogInfo::where('blogTitle','like','%'.$search.'%')
                ->where('t_blog_info.blogTypeId','like','%'.$blogtypeId.'%')
                ->where('t_blog_info.isPublic','=',1)
                ->where('t_blog_info.isSuspicious','=',0)
                ->join('t_user','t_user.userOnlyId','t_blog_info.user_id')
                ->orderBy($types,'desc')
                ->offset($current)->limit($pagecount)
                ->get(['t_blog_info.*','t_user.name']);

        foreach ($bloginfo as $bloginfos){
            $bloginfos->blogContent = mb_substr($bloginfos->blogContent , 0 , 100);
        }

        return json_encode(['blogNum'=>$blogNum,'data'=>$bloginfo],JSON_UNESCAPED_UNICODE);
    }

    ///blog类别展示
    public function GetBlogType(){
        $type1 = BlogType::get(['blogTypeOnlyId','name']);          //系统类别
        $type2 = uBlogType::where('userId','=',Auth::guard('api')->user()->userOnlyId)->get(['ublogTypeOnlyId','name']);        //用户自定义类别

        return json_encode(['msg_code' =>0,'data'=>$type1,'udata'=>$type2],JSON_UNESCAPED_UNICODE);
    }

    ///blog具体信息
    public function BlogInfo(Request $request){
        //验证
        if(is_null($request->blogId)){
            return json_encode(['msg_code'=>1,'msg'=>'你没告诉我你要哪篇blog!'],JSON_UNESCAPED_UNICODE);
        }

        $bloginfo = BlogInfo::where('blogOnlyId','=',$request->blogId)
            ->join('t_user','t_blog_info.user_id','t_user.userOnlyId')
            ->first(['t_blog_info.*','t_user.name','t_user.userOnlyId','t_user.email']);
        if(!is_null($bloginfo)){
            //记录用户动作（用作今后推荐算法和历史记录）
            $userAction = new UserAction;
            $userAction->userId =Auth::guard('api')->user()->userOnlyId;
            $userAction->blogInfoId = $request->blogId;
            if(!$userAction->save()){
                return json_encode(['msg_code'=>1,'msg'=>'记录用户操作有误！'],JSON_UNESCAPED_UNICODE);
            }
        }else{
            return json_encode(['msg_code'=>1,'msg'=>'该文章已被删除或不存在！'],JSON_UNESCAPED_UNICODE);
        }

        //获取评论
        $comment = $this->Comment($request->blogId);
        //评论是否被当前用户赞过
        $comment = $this->IsLike($comment);

        //处理阅读人数
        $this->ReadNum($request->blogId);

        //只要不是0，则全部识别为可疑   1：管理员审核确定文章有误（石锤）  2：系统自动判断为可疑  3：用户举报
       if($bloginfo->user_id !=Auth::guard('api')->user()->userOnlyId){
            if($bloginfo->isSusoicious == 1){
                return json_encode(['msg_code'=>1,'msg'=>'该文章存在非法内容不可访问!'],JSON_UNESCAPED_UNICODE);
            }elseif($bloginfo->isSusoicious == 2){
                return json_encode(['msg_code'=>1,'msg'=>'系统判断文章存在侮辱性言语，请等待管理员审核!'],JSON_UNESCAPED_UNICODE);
            }elseif($bloginfo->isSusoicious == 3){
                return json_encode(['msg_code'=>1,'msg'=>'该文章被多名用户举报，请等待管理员审核!'],JSON_UNESCAPED_UNICODE);
            }else{
                return json_encode(['msg_code'=>0,'data1'=>$bloginfo,'data2'=>$comment],JSON_UNESCAPED_UNICODE);
            }
       }else{
            return json_encode(['msg_code'=>0,'data1'=>$bloginfo,'data2'=>$comment],JSON_UNESCAPED_UNICODE);
        }
    }
            //blog系统标签展示
            public function BlogTag(){
            $blogtag = BlogTag::get(['content']);
            return json_encode(['msg_code'=>0,'data'=>$blogtag],JSON_UNESCAPED_UNICODE);
        }

            //获取评论信息
            public function Comment($blogId){
            //管理员可获取所有评论
            if(Auth::guard('api')->user()->role == 0)
            {
                $returns = Comment::where('blogId','=',$blogId)
                    ->join('t_user','t_comment.userId','t_user.userOnlyId')
                    ->select('t_comment.*','t_user.name')
                    ->get();
        }
        else{
            $returns = Comment::where('blogId','=',$blogId)
                ->where('IsHide','=',0)
                ->join('t_user','t_comment.userId','t_user.userOnlyId')
                ->select('t_comment.*','t_user.name')
                ->get();
        }
        return $returns;
    }

    //判断博客评论中有几个是被当前用户赞过的
    public function IsLike($comment){
        $userinfo = User::where('userOnlyId','=',Auth::guard('api')->user()->userOnlyId)->value('doLikeComment');
        $array = is_null(json_decode($userinfo))?[]:json_decode($userinfo);
        foreach ($comment as $com){
            if(in_array($com->commentOnlyId,$array)){
                $com->IsLiked = 1;
            }
        }
        return $comment;
    }

    //处理阅读人数
    public function ReadNum($blogId){
        $bloginfo = BlogInfo::where('blogOnlyId','=',$blogId)->first();
        if($bloginfo->user_id != Auth::guard('api')->user()->userOnlyId){
            $bloginfo->readNum += 1;
        }
        $bloginfo->save();
    }
    //
    //博客标题返回
    public function BlogTitle(Request $request){
        $title = DB::table('t_blog_info')
            ->where('blogTitle','like','%'.$request->title.'%')
            ->where('isSuspicious','=',0)
            ->select('blogTitle as value')
            ->take(8)
            ->get();
        return json_encode(['msg'=>0,'data'=>$title],JSON_UNESCAPED_UNICODE);
    }

    //獲取評論信息
    public function GetComment(Request $request){
        //验证字段
         if(is_null($request->blogId)||$request->blogId==""){
             $msg[] = '请选择想要获取的文章';
             $msg_code = 1;
             return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
         }
         //验证该博客是否存在
        $isset = BlogInfo::where('blogOnlyId','=',$request->blogId)->get();
        if(isset($isset)){
            $comment = Comment::where('blogId','=',$request->blogId)
                ->where('IsHide','=',0)
                ->get();
            //dd($comment);
            $msg_code = 0;
            return json_encode(['msg_code'=>$msg_code,'data'=>$comment]);
        }else{
            $msg[]='博客不存在！';
            $msg_code = 1;
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
    }

    //用户自定义blog类型新增、获取、删除
    public function CreateBlogType(Request $request){
        //简单的非空验证
        $validatedData = $request->validate([
            'name' => 'required|max:4'
        ],[
            'name.required'=>'类型名字必填！',
            'name.max'=>'类型名不超过四个字！'
        ]);

        //验证通过后做是否重复判断
        $ubogtype = uBlogType::where('name','=',$request->name)->where('userId','=',Auth::guard('api')->user()->userOnlyId)->get();
        $count = uBlogType::where('userId','=',Auth::guard('api')->user()->userOnlyId)->count();
        if(empty($ubogtype)){
            return json_encode(['msg_code'=>1,'data'=>'已经存在相同类型！'],JSON_UNESCAPED_UNICODE);
        }
        //用户自定义上限为10个
        if($count<5){
            $ubogtype = new uBlogType();
            //dd('成功啦！');
            $ubogtype->ublogTypeOnlyId = Uuid::uuid1();
            $ubogtype->userId = Auth::guard('api')->user()->userOnlyId;
            $ubogtype->name = $request->name;
            $ubogtype->remark = $request->remark;
            if($ubogtype->save()){
                return json_encode(['msg_code'=>0,'data'=>'保存成功！'],JSON_UNESCAPED_UNICODE);
            }else{
                return json_encode(['msg_code'=>1,'data'=>'保存失败！'],JSON_UNESCAPED_UNICODE);
            }
        }else{
            return json_encode(['msg_code'=>1,'data'=>'用户只能自定义5个类型！'],JSON_UNESCAPED_UNICODE);
        }
    }

    //用户自定义blog获取
    public function GetuBlogType(){
        $ublogTyoe = uBlogType::where('userId','=',Auth::guard('api')->user()->userOnlyId)->get();
        return json_encode(['msg_code'=>0,'data'=>$ublogTyoe],JSON_UNESCAPED_UNICODE);
    }

    //删除自定义blogtype
    public function DeleteuBlogType(Request $request){

        $validatedData = $request->validate([
            'ublogTypeOnlyId'=>'required'],
            ['ublogTypeOnlyId.required'=>'请选择想要删除的类型！']);

        $ublogtype = uBlogType::where('ublogTypeOnlyId','=',$request->ublogTypeOnlyId)->get();
        if($ublogtype->delete()){
            return json_encode(['msg_code'=>0,'data'=>'删除成功！'],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['msg_code'=>0,'data'=>'删除失败！'],JSON_UNESCAPED_UNICODE);
        }
    }

}
