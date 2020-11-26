<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*****************************************************************************/
Route::get('/', function () {
    return view('welcome');
});
Route::get('/test',function (){
    return view('admin.blogReview');
});
//测试redis
Route::get('/redis',function (){
    $info =\Illuminate\Support\Facades\Redis::keys('*');;
    dd($info);
});
Route::get('/blogList','Admin\AdminController@BlogList');
//测试贝叶斯算法路由
Route::get('/test_Bayesian',"Controller@test_Bayesian");
//加密测试
Route::get('/test_jiami','Controller@test_jiami');
/*****************************************************************************/
Route::get('/admin/login','Admin\AdminController@Login')->name('Login');
Route::post('/login','Admin\AdminController@Loging');
Route::post('/test1','Admin\AdminController@Testdatafrom');
Route::get('/redis','Controller@TestRedis');


Route::get('/GetAction','Admin\AdminController@GetAction');//

Route::group(['middleware' => 'auth'], function () {
    //获取数据
    Route::get('/home',function (){
        return view('base');
    })->name('home');

    Route::get('/userinfo','Admin\AdminController@UserInfo');
    Route::get('/logout','Admin\AdminController@Logout');
    Route::get('/blogList','Admin\AdminController@BlogList');
    Route::get('/GetBlogInfo','Admin\AdminController@GetBlogInfo');
});
