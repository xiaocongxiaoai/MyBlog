<?php

use Illuminate\Http\Request;



//use Illuminate\Routing\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//允许跨域
//Route::options('/{all}', function(Request $request) {
//    $origin = $request->header('ORIGIN', '*');
//    header("Access-Control-Allow-Origin: $origin");
//    header("Access-Control-Allow-Credentials: true");
//    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
//    header('Access-Control-Allow-Headers: Origin, Access-Control-Request-Headers, SERVER_NAME, Access-Control-Allow-Headers, cache-control, token, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie, USERID, SIGN, TIME');
//    $token = $request->header('token');
//    if(!$token){
//        json_encode(['msg_code'=>401,'msg'=>'授权失败，请检查token'],JSON_UNESCAPED_UNICODE);
//    }else{
//        $res = (new Token())->verifyToken($token);
//        if(!$res){
//            json_encode(['msg_code'=>402,'msg'=>'token失效，请重新获取'],JSON_UNESCAPED_UNICODE);
//        }
//    }
//    return json_encode(['msg_code'=>1,'msg'=>'兄弟，你访问了我！'],JSON_UNESCAPED_UNICODE);;
//})->where(['all' => '([a-zA-Z0-9-]|/)+']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
////测试接口
Route::get('/test',"API\APIControllers@fenci");
//Route::get('/hhh/test',"API\APIControllers@GetBlogTag");
//
////注册接口
Route::post('/blog/regisert',"API\Auth\APIRegisertController@Regisert");

///登录接口
//测试验证token中间件
Route::post('/blog/login','API\Auth\APILoginController@Login')->name('Login');
Route::get('/blog/test','API\Auth\APILoginController@test_login');


//登出接口
Route::get('/blog/loginOut','API\Auth\APILoginOutController@LoginOut');



Route::group(['middleware' => 'auth.api'], function () {
    //创建博客
    Route::post('/blog/create','API\UserController@createBlog');
    //获取博客类别
    Route::get('/blog/getType','API\BlogController@GetBlogType');
    //获取博客详情
    Route::get('/blog/getBlogInfo','API\BlogController@BlogInfo');
    //获取系统标签
    Route::get('/blog/getTag','API\BlogController@BlogTag');
    //获取blog列表
    Route::get('/blog/search','API\BlogController@Search');
    //获取自己博客列表
    Route::get('/blog/myBlog','API\UserController@MyBlog');
    //更改我的blog
    Route::post('/blog/change','API\UserController@BlogChange');
    //查看历史记录
    Route::get('/blog/history','API\UserController@History');
    //我的博客标题
    Route::get('/blog/MyBlogTitle','API\UserController@MyBlogTitle');
    //所有博客标题
    Route::get('/blog/BlogTitle','API\BlogController@BlogTitle');
    //上传图片
    Route::post('/blog/ImgUp','API\ImgController@ImgUp');

    Route::post('/test',function (){
        return json_encode(['test'=>'求求你'],JSON_UNESCAPED_UNICODE);
    });

});

