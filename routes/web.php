<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
Route::group(['middleware'  => ['admin']], function() {
Route::get('/admin/{any}', 'PagesController@admin ')->where('any', '.*');
});
Route::group(['middleware'  => ['vendor']], function() {
	Route::get('/vendor/{any}', 'PagesController@vendor')->where('any', '.*');
});
Route::group(['middleware'  => ['accounts']], function() {
	Route::get('/accounts/{any}', 'PagesController@accounts')->where('any', '.*');
});



// Route::get('/', function () {
//         return view('welcome');
//     })->middleware('auth');
/*
Route::get('/', function () {
    return view('auth.login');
});
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'checkLogin'])->name('checkLogin');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


/********* Login via OTP **********/
Route::get('otplogin', [App\Http\Controllers\AuthController::class, 'index']);
Route::post('sendOTP', [App\Http\Controllers\AuthController::class, 'sendOTP']);
Route::post('registerUser', [App\Http\Controllers\AuthController::class, 'registerUser']);
Route::post('verifyOTP', [App\Http\Controllers\AuthController::class, 'verifyOTP']);
/********* Login via OTP **********/

Route::get('signup', [App\Http\Controllers\AuthController::class, 'signup']);


