<?php

namespace App\Http\Controllers\API\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\Session;

class APILoginController extends Controller
{
    //登录认证
    public function Login(Request $request){
        date_default_timezone_set(PRC);  //设定正确的时间
        //$msg[]=null;            //具体信息
        $msg_code = 0;          //返回成功类型信息
        //可以用用户名或者邮箱登录
        //后端验证信息
        Cache::put('login',0);   //Cache['login']默认为0  未登陆
        $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        if(is_null($request->name)){
            $msg_code = 1;
            $msg[]="用户名或邮箱不能为空";
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
        if(is_null($request->password)){
            $msg_code = 1;
            $msg[]="密码不能为空";
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }

        //完成登陆验证
        //使用laravel自带认证
        if(Auth::attempt(['name' =>$request->name,'password'=>$request->password])||Auth::attempt(['email'=>$request->name,'password'=>$request->password])){
            $msg_code = 0;
            $msg[]="登录成功！";
            //设置登录状态 1为登录

            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg,'UserId'=>Auth::user()->userOnlyId,'apitoken'=>Auth::user()->api_token],JSON_UNESCAPED_UNICODE);
        }else{
            //否则登录失败
            $msg_code = 1;
            $msg[]="用户名或密码错误！";
            return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }


    }

    function test_login(Request $request){

        dd(456);

    }
}
