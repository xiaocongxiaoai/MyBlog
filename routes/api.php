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
//Route::group(['middleware' => 'web'], function () {
//    Route::post('/blog/login','API\Auth\APILoginController@Login')->name('Login');
//
//});
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
Route::get('/blog','API\BlogController@Search')->middleware('login');

Route::group(['middleware' => 'auth.api'], function () {

    Route::post('/blog/create','API\UserController@createBlog');

});

