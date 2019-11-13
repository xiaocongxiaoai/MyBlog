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

//博客
//oute::get('/blog','API\BlogController@Search')->middleware('login');

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
});

