<?php

namespace App\Http\Controllers\API\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class APIRegisertController extends Controller
{
    //
    //注册方法，判断必填项

    public function Regisert(Request $Requst){
        date_default_timezone_set(PRC);
        //定义返回信息
        $msg_code=0; //默认是返回正确返回值
        $msg=[];
        //用于判断是是否存在用户
        $user = User::where('name','=',$Requst->name)->first();
        $user_email = User::where('email','=',$Requst->email)->first();
        //return response()->json(['title'=>"这是你想传给我的信息吗：".$test])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        //验证字段

        //验证邮箱格式
        $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';

        if(isset($Requst->name)&&isset($Requst->password)){
            if(strlen($Requst->name)>30){
                $msg_code=1; //返回错误类型提示信息
                $msg[]='用户名不能超过十个字';
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }
            if(strlen($Requst->name)<2){
                $msg_code=1; //返回错误类型提示信息
                $msg[]='用户名不能太短';
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }
            //Web安全
            if(strpos($Requst->name,'>')-strpos($Requst->name,'<')>0){
                $msg_code=1; //返回错误类型提示信息
                $msg[]='用户名不能使用非法字符！再次使用，系统将会拉黑IP！';
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }
            if(!preg_match($mode,$Requst->email)){
                $msg_code = 1;
                $msg[]="邮箱格式错误";
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }
            if(isset($user)){
                $msg_code=1; //返回错误类型提示信息
                $msg[]='该用户名已被使用';
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }elseif(isset($user_email)){
                $msg_code = 1;
                $msg[] = '该邮箱已被注册！';
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }else{
                //字段认证通过还需进行注册还有token认证
                $regisert = User::create([
                    'userOnlyId' =>Uuid::uuid1(),           //生成唯一ID
                    'name' => $Requst->name,
                    'password' => Hash::make($Requst->password),    //哈希加密
                    'email' =>is_null($Requst->email) ? null:$Requst->email,
                    'api_token' => Str::random(60),         //生成令牌
                    'summary' =>is_null($Requst->summary) ? "无":$Requst->summary,    //信息
                    'phoneNum' =>is_null($Requst->phoneNum)? null:$Requst->phoneNum, //手机号
                    'isPublic' =>isset($Requst->isPublic)? $Requst->isPublic:0,      //是否公开个人信息，默认为0
                    'role' => 1                         //0为管理员  1为普通用户
                    //'created_at' =>Carbon::now()->timestamp,                        //获取当前时间并且转化成时间戳
                ]);

                //做存储判断
                if($regisert->save()){
                    $msg_code = 0;  //返回正确类型提示信息
                    $msg[] = "注册成功！请登录";
                }else{
                    $msg_code = 1;  //返回正确类型提示信息
                    $msg[] = "注册失败！未知错误请联系管理员！";
                }
                return json_encode(['msg_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
            }


        }else{
            $msg_code=1; //返回信息为错误类型
            $msg[]='用户名或不能为空';
            return json_encode(['ms_code'=>$msg_code,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
        }
    }



}
