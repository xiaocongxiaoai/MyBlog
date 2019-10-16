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
//Route::post('/blog/login','API\Auth\APILoginController@Login')->middleware('auth.api');
Route::post('/blog/login','API\Auth\APILoginController@Login')->name('Login');

//登出接口
Route::get('/blog/loginOut','API\Auth\APILoginOutController@LoginOut');

//博客
Route::get('/blog','API\BlogController@Search')->middleware('login');
Route::post('/blog/create','API\UserController@createBlog');
