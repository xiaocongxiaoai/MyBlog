<?php

namespace App\Http\Controllers\API;

use App\BlogInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    //查询blog/展示

    public function Search(Request $request){
        $search = is_null($request->search)?"":$request->search;
        //当前页
        $index = intval(is_null($request->index)?1:$request->index);
        //每页展示数
        $pagecount =intval(is_null($request->pagecount)?20:$request->pagecount);
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

        //blog总数
        $blogNum = BlogInfo::where('blogTitle','like','%'.$search.'%')
            ->where('isPublic','=',1)
            ->where('isSuspicious','=',0)
            ->count();

        //blog分页数据
        $current = ($index-1)*$pagecount;    //开始取数据的位置

        $bloginfo = BlogInfo::where('blogTitle','like','%'.$search.'%')
            ->where('blogTypeId','like','%'.$blogtypeId.'%')
            ->where('isPublic','=',1)
            ->where('isSuspicious','=',0)
            ->orderBy($types,'desc')
            ->offset($current)->limit($pagecount-1)
            ->get();

        return json_encode(['blogNum'=>$blogNum,'data'=>$bloginfo],JSON_UNESCAPED_UNICODE);

    }
    //blog类别展示
    //blog具体信息


    //blog系统标签展示

}
