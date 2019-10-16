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

Route::get('/', function () {
    return view('welcome');
});

//获取数据
//Route::get('/test',"Controller@test");
//Route::get('/test2',"Controller@test2");
Route::get('/test3',"Controller@test2");

//测试贝叶斯算法路由
Route::get('/test_Bayesian',"Controller@test_Bayesian");

//加密测试
Route::get('/test_jiami','Controller@test_jiami');
