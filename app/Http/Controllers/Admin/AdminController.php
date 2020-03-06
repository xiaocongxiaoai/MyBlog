<?php

namespace App\Http\Controllers\Admin;

use App\BlogInfo;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Table;

class AdminController extends Controller
{
    /**页面跳转**/
    // 登录系统
    public function Login(){
        return view('admin.login');
    }


    /**后端逻辑**/
    //    //用户的管理(增删查改)
    //    //blog的审批(计划用分类算法，推荐给admin可疑的文章进行审批操作)
    //    //blog类别的管理(增删查改)
    //    //blog系统标签的管理(增删查改)


    //    处理登录认证
    public function Loging(Request $request){
        //判断
        if(is_null($request->name)){
            return json_encode(['msg_code'=>1,'msg'=>'请填写账号！']);
        }
        if(is_null($request->password)){
            return json_encode(['msg_code'=>1,'msg'=>'密码不能为空！']);
        }
        if (Auth::attempt(['name' => $request->name, 'password' => $request->password])) {
            // 认证通过...
            if(Auth::attempt(['name' => $request->name, 'password' => $request->password,'role'=>1])){
                return json_encode(['msg_code'=>0,'msg'=>'登陆成功！']);
            }else{
                return json_encode(['msg_code'=>1,'msg'=>'不是管理员！']);
            }
        }else{
            return json_encode(['msg_code'=>1,'msg'=>'密码或账号错误']);
        }
    }

    //    管理员信息及系统博客提交信息
    public function UserInfo(Request $request){

        $userinfos = User::where('userOnlyId',$request->userId)->first();
        $bloginfos = DB::table('t_blog_info')
            ->join('t_user','t_blog_info.user_id','=','t_user.userOnlyId')
            ->select('t_blog_info.*','t_user.name')
            ->orderBy('t_blog_info.created_at',"desc")
            ->take(6)
            ->get();
        foreach ($bloginfos as $bloginfo){
            if(mb_strlen($bloginfo->blogContent)>100)
            $bloginfo->blogContent = mb_substr($bloginfo->blogContent , 0 , 100)."...";
            $bloginfo->blogContent = strip_tags($bloginfo->blogContent);
        }
        return json_encode(['userinfos'=>$userinfos,'bloginfos'=>$bloginfos],JSON_UNESCAPED_UNICODE);
    }

    //登出
    public function LogOut(){
        Auth::logout();
        return json_encode(['msg_code'=>0,'msg'=>'登出！'],JSON_UNESCAPED_UNICODE);
    }

    //获取博客列表信息
    public function BlogList(Request $request){
        $index = is_null($request->index)?1: $request->index;
        $count = is_null($request->count)?15: $request->count;
        $current = ($index-1)*$count;
        $blogInfos = DB::table('t_blog_info')
            ->join('t_user','t_blog_info.user_id','=','t_user.userOnlyId')
            ->join('t_blog_type','t_blog_info.blogTypeId','=','t_blog_type.blogTypeOnlyId')
            ->select('t_blog_info.*','t_user.name','t_blog_type.name as type')
            ->offset($current)->limit($count)->get();
        $counts = BlogInfo::all()->count();

        return json_encode(['msg_code'=>0,'data'=>$blogInfos,'counts'=>$counts],JSON_UNESCAPED_UNICODE);
    }

    public function GetBlogInfo(Request $request){
        if(is_null($request->blogOnlyId)){

            return json_encode(['msg_code'=>1,'msg'=>'请选择想查看的博客'],JSON_UNESCAPED_UNICODE);
        }
        $bloginfo = BlogInfo::where('blogOnlyId','=',$request->blogOnlyId)->get();

        return json_encode(['msg_code'=>0,'msg'=>'OK','data'=>$bloginfo]);

    }
}
