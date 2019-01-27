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

Route::get('/testVue', function () {
    return view('vue.test-vue');
});

Route::get('/query', 'PodcastController@store');

Route::get('/testVue/{any}', function () {
    return view('vue.test-vue');
})->where('any','.*');

//测试
Route::get('/exec', 'PodcastController@exec');

// 测试Supervisor的使用
Route::get('/send-file', 'PodcastController@sendFile');

// 测试发邮件
Route::get('/send-mail', 'PodcastController@sendMail');
