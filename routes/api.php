<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceCostController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1'], function () {
Route::get('insertAllOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
Route::get('getOrderdetail',[App\Http\Controllers\OrderController::class,'OrderDetail']);
Route::get('getProductdetail',[App\Http\Controllers\ProductController::class,'productDetail']);
Route::get('getDashbaord',[App\Http\Controllers\DashboardController::class,'Dashboard_Detail']);
Route::get('walletdetail',[App\Http\Controllers\WalletController::class,'Wallet_detail']);
Route::get('reportdetail',[App\Http\Controllers\ReportController::class,'Report_detail']);
Route::get('Accountdetail',[App\Http\Controllers\AccountController::class,'Account_detail']);
Route::get('get_orderProfile',[App\Http\Controllers\ReportController::class,'ReportProfile_detail']);
     
   
  //Route::get('getProductdetail123',[App\Http\Controllers\OrderController::class,'OrderDetail']);
  Route::get('getjson',[App\Http\Controllers\JsonController::class,'getJson']);
    
}); 

