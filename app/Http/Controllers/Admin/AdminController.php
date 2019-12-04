<?php

namespace App\Http\Controllers\Admin;

use App\BlogInfo;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //    //用户的管理(增删查改)
    //    //blog的审批(计划用分类算法，推荐给admin可疑的文章进行审批操作)
    //    //blog类别的管理(增删查改)
    //    //blog系统标签的管理(增删查改)
    public function Login(){
        return view('admin.login');
    }
    public function UserInfo(Request $request){

        $userinfos = User::where('userOnlyId',$request->userId)->first();
//        $bloginfos = BlogInfo::all()
//            ->join('t_user','t_blog_info.user_id','t_user.userOnlyId')
//            ->take(6);
        $bloginfos = DB::table('t_blog_info')
            ->join('t_user','t_blog_info.user_id','=','t_user.userOnlyId')
            ->select('t_blog_info.*','t_user.name')
            ->orderBy('t_blog_info.created_at',"desc")
            ->take(6)
            ->get();
        foreach ($bloginfos as $bloginfo){
            if(mb_strlen($bloginfo->blogContent)>100)
            $bloginfo->blogContent = mb_substr($bloginfo->blogContent , 0 , 100)."...";
            //$bloginfo->blogContent += "...";
            $bloginfo->blogContent = strip_tags($bloginfo->blogContent);
        }
        return json_encode(['userinfos'=>$userinfos,'bloginfos'=>$bloginfos],JSON_UNESCAPED_UNICODE);
    }
}
