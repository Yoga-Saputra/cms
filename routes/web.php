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

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('categories', 'CategoryController');
    Route::resource('posts', 'PostController');
    Route::resource('tags', 'TagController');
    Route::get('/trashed-post', 'PostController@trashed')->name('trashed-post.index');
    Route::put('/posts/restore-trashed-post/{post}', 'PostController@restorePost')->name('restore-trashed-post.index');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['admin', 'auth']], function () {
    Route::get('/users', 'UserController@index')->name('users');
    Route::post('/users/{user}/make-admin', 'UserController@makeAdmin')->name('users.make-admin');
});
