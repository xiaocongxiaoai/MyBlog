<?php

namespace App\Http\Controllers\API;

use App\BlogInfo;
use App\Comment;
use App\User;
use App\UserAction;
use DemeterChain\B;
use Faker\Provider\Uuid;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
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
    //评论置顶

    //点赞博客
    public function Like(Request $request){
        //是否选中
        if(is_null($request->blogOnlyId)){
            $msg_code = "1";
            $msg[]='请选择点赞的博客！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //是否存在
        if(is_null(BlogInfo::where('blogOnlyId','=',$request->blogOnlyId)->where('isSuspicious','=',0)->first())){
            $msg_code = "1";
            $msg[]='博客已被删除或涉及敏感信息无法评价！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //点赞
        $bloginfo = BlogInfo::where('blogOnlyId','=',$request->blogOnlyId)->first();
        $bloginfo->likeNum += 1;
        if($bloginfo->save()){
            $msg_code = "0";
            $msg[]='OK';
        }else{
            $msg_code = "0";
            $msg[]='NO';
        }
        return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
    }
    //評論他人博客
    public function Comment(Request $request){
        //是否选中
        if(is_null($request->blogOnlyId)){
            $msg_code = "1";
            $msg[]='请选择评价的博客！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //是否存在
        if(is_null(BlogInfo::where('blogOnlyId','=',$request->blogOnlyId)->where('isSuspicious','=',0)->first())){
            $msg_code = "1";
            $msg[]='博客已被删除或涉及敏感信息无法评价！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //评论内容
        if(is_null($request->comment)){
            $msg_code = "1";
            $msg[]='评论内容不能为空！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        $request->comment = strip_tags($request->comment);
        //插入新增数据
        $commentinfo = new Comment();
        $commentinfo->commentOnlyId = \Ramsey\Uuid\Uuid::uuid1();
        $commentinfo->userId = Auth::guard('api')->user()->userOnlyId;
        $commentinfo->content = $request->comment;
        $commentinfo->blogId = $request->blogOnlyId;
        $commentinfo->likeNum = 0;
        $commentinfo->noLikeNum = 0;
        $commentinfo->IsHide = 0;
        if($commentinfo->save()){
            $msg_code = "0";
            $msg[]='评论成功！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }else{
            $msg_code = "1";
            $msg[]='评论失败！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }


    }
    //点赞/点踩评论
    public function LikeOnLike(Request $request){

        //是否选中
        if(is_null($request->commentOnlyId)){
            $msg_code = "1";
            $msg[]='请选择点赞/踩的评论！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //是否存在
        if(is_null(Comment::where('commentOnlyId','=',$request->commentOnlyId)->where('IsHide','=',0)->first())){
            $msg_code = "1";
            $msg[]='该评论已经被删除！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //是否已经点过赞
        //转为数组做判断

        $infos=User::where('userOnlyId','=',Auth::guard('api')->user()->userOnlyId)->value('doLikeComment');
        $array = is_null(json_decode($infos))?[]:json_decode($infos);
        if(!is_null($infos)){
            if(in_array($request->commentOnlyId,$array)){
//                $msg_code = "1";
//                $msg[]='已经点赞过该评论！';
//                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
                $request->IsLike = 0;
            }
        }

        $commentinfo = Comment::where('commentOnlyId','=',$request->commentOnlyId)->first();
        $userinfo = User::where('userOnlyId','=',Auth::guard('api')->user()->userOnlyId)->first();
        if(is_null($request->IsLike)?1:$request->IsLike == 1)
        {

            //点赞
            $commentinfo->likeNum += 1;
            array_push($array,$request->commentOnlyId);
            $userinfo->doLikeComment = json_encode($array) ;
        }
        else{
            //取消点赞
            $commentinfo->likeNum -= 1;
            $array = array_diff($array,[$request->commentOnlyId]);
            $userinfo->doLikeComment = is_null(json_encode($array))?null:json_encode($array);

        }
        if($commentinfo->save()&&$userinfo->save()){
            $msg_code = "0";
            $msg[]='OK';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }else{
            $msg_code = "1";
            $msg[]='NO';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }

    }

    //隐藏评论(仅限博主)
    public function DelComment(Request $request){
        //验证是否选择删除列
        if(is_null($request->commentOnlyId)){
            $msg_code = "1";
            $msg[]='请选择删除的评论！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //验证评论是否存在
        $IsHave = Comment::where('commentOnlyId','=',$request->commentOnlyId)->get();
        if(!isset($IsHave)){
            $msg_code = "1";
            $msg[]='该评论已被删除';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        //判断当前用户是否有删除评论的权力
        $info = BlogInfo::where('blogOnlyId','=',$request->blogOnlyId)
        ->get();
        $userid = $info->user_id;
        if($userid != Auth::guard('api')->user()->userOnlyId){
            $msg_code = "1";
            $msg[]='您不能删除这条评论！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }

        //隐藏评论(假删除)
        $commentinfo = Comment::where('commentOnlyId','=',$request->commentOnlyId)->get();
        $commentinfo->IsHide = 1;
        if($commentinfo->save()){
            $msg_code = "0";
            $msg[]='删除成功！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }else{
            $msg_code = "1";
            $msg[]='删除失败！';
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }

    }

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
        $bloginfo->isSuspicious = $this->test_Bayesians($request->title);               //调用算法测试标题合法性（是否带有侮辱性）
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
            ->where('user_id','=',Auth::guard('api')->user()->userOnlyId)
            ->where('blogTypeId','like','%'.$blogtypeId.'%')
            ->where('blogTitle','like','%'.$request->title.'%')
            ->where('isSuspicious','=',$isGood)
            ->count();
        //blog分页数据
        $current = ($index-1)*$pagecount;    //开始取数据的位置
        $goodinfo = BlogInfo::
            where('user_id','=',Auth::guard('api')->user()->userOnlyId)
            ->where('blogTypeId','like','%'.$blogtypeId.'%')
            ->where('blogTitle','like','%'.$request->title.'%')
            ->where('isSuspicious','=',$isGood)
            ->orderBy($types,'desc')
            ->offset($current)->limit($pagecount)
            ->get();
        return json_encode(['blogNum'=>$blogNum,'data'=>$goodinfo],JSON_UNESCAPED_UNICODE);
    }

    //更改我写的blog
    public function BlogChange(Request $request){
        //验证输入信息
        if(is_null($request->blogId)){
            return json_encode(['msg_code'=>1,'msg'=>'选择想要修改的博客！'],JSON_UNESCAPED_UNICODE);
        }
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
        $bloginfo->blogOnlyId = $request->blogId;
        $bloginfo->blogContent = $request->blogContent;
        $bloginfo->blogTypeId = $request->blogType;
        $bloginfo->blogUserTypeId = is_null($request->blogUserType)?"":$request->blogUserType;
        $bloginfo->blogTag = is_null($request->blogTag)?"无":$request->blogTag;
        $bloginfo->isPublic = is_null($request->isPublic)?1:$request->isPublic;
        if($bloginfo->save()){
            return json_encode(['msg_code'=>0,'msg'=>'更新成功！',JSON_UNESCAPED_UNICODE]);
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
            ->select('blogTitle as value')
            ->take(8)
            ->get();
        return json_encode(['msg'=>0,'data'=>$title],JSON_UNESCAPED_UNICODE);
    }

    public function test_Bayesians($title){
        $listOposts = learns();

        $listClasses = $listOposts[0];
        $listOposts = $listOposts[1];

        //得到词库
        $myVocabList = createVocabList($listOposts);
        $trainMat=[];
        foreach ($listOposts as $listOpostses){
            $trainMat[] = setOfWords2Vec($myVocabList,$listOpostses);
        }
        //训练算法
        $returns = trainNB($trainMat,$listClasses);
        //测试数据
        //分词必要设置↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        ini_set('memory_limit','1024M');
        Jieba::init();
        Finalseg::init();
        //分词必要设置↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        $testWords = $title;
        $testWords = Jieba::cut($testWords);
        //开始
        $testWords_return = setOfWords2Vec($myVocabList,$testWords);
        $end = $this->classifyNBs($testWords_return,$returns[1],$returns[0],$returns[2]);//预测结果1为侮辱性，0为正常
        return $end;
    }

    //贝叶斯分类器
    public function classifyNBs($vec2Classify,$p0vec,$p1vec,$pClass1){
        foreach(range(0,count($vec2Classify)-1) as $i){
            $t1[] = $p1vec[$i]*$vec2Classify[$i];     //(P(Xn|A)/P(Xn))*P(A)
            $t0[] = $p0vec[$i]*$vec2Classify[$i];     //(P(Xn|B)/P(Xn))*P(B)
        }
        $p1 = array_sum($t1)+log($pClass1);
        $p0 = array_sum($t0)+log(1.0-$pClass1);
        if($p1>$p0){
            return 1;//"你再骂！？";
        }else{
            return 0;//"你是个懂礼貌的好孩子";
        }
    }
}
