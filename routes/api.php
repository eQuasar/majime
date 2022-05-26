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
    // Route::apiResource('billings', 'BillingController');
    // Route::apiResource('billing', 'BillingController');
	Route::get('insertAllOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
	
    Route::post('getOrderDetails', 'OrderController@getOrderDetails')->name('getOrderDetails');
	Route::post('getProductdetail',[App\Http\Controllers\ProductController::class,'productDetail']);
	Route::get('getDashbaord',[App\Http\Controllers\DashboardController::class,'Dashboard_Detail']);
	Route::get('walletdetail',[App\Http\Controllers\WalletController::class,'Wallet_detail']);
	Route::get('reportdetail',[App\Http\Controllers\ReportController::class,'Report_detail']);
	Route::get('Accountdetail',[App\Http\Controllers\AccountController::class,'Account_detail']);
	Route::get('get_orderProfile',[App\Http\Controllers\ReportController::class,'ReportProfile_detail']);
	//Route::get('order_Search',[App\Http\Controllers\OrderController::class,'Order_Search']);
	Route::post('order_Search', 'OrderController@Order_Search')->name('Order_Search');
	Route::post('status_details', 'ReportController@status_details')->name('status_details');
	Route::get('insertAllOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
	// Route::get('order_Profile/{oid}',[App\Http\Controllers\OrderController::class,'OrdersProfile']);
    Route::get('order_Profile/{oid}', 'OrderController@order_Profile')->name('order_Profile');
	Route::get('order_items/{oid}', 'OrderController@order_items')->name('order_items');
	
	Route::get('get_order_data', 'JsonController@get_order_data')->name('get_order_data');
	
	Route::get('getOrderOnStatus/{vid}/{status}', 'OrderController@getOrderOnStatus')->name('getOrderOnStatus');

	Route::get('getpackdetail/{vid}', 'OrderController@getPackdetail')->name('getpackdetail'); 
	// Route::get('getConfirmeddetail/{vid}', 'OrderController@getConfirmeddetail')->name('getConfirmeddetail');
	

     Route::post('getOrderdetail',[App\Http\Controllers\OrderController::class,'OrderDetail']);
     // Route::post('getOrderdetail',[App\Http\Controllers\OrderController::class,'OrderDetail']);
    //Route::get('getProductdetail123',[App\Http\Controllers\OrderController::class,'OrderDetail']);
	
    Route::apiResource('vendors', 'VendorsController');
  	Route::get('getAllLinks',[App\Http\Controllers\JsonController::class,'getAllLinks']);
  	// Route::get('getJson',[App\Http\Controllers\JsonController::class,'getJson']);
	Route::post('getJson', 'JsonController@getJson')->name('getJson');
	Route::post('changeStatus', 'OrderController@changeStatus')->name('changeStatus');
	Route::post('assignAWB', 'OrderController@assignAWB')->name('assignAWB');
	Route::post('assignAWBOrder', 'OrderController@assignAWBOrder')->name('assignAWBOrder');
	Route::get('return_order', 'OrderController@return_order')->name('return_order');
	Route::post('printSlip', 'OrderController@printSlip')->name('printSlip');
	Route::post('printOrderSlip', 'OrderController@printOrderSlip')->name('printOrderSlip');
	Route::post('getVid', 'AuthController@getVid')->name('getVid');
	Route::post('addWayData', 'WayDataController@store')->name('addWayData');
	Route::post('updateWayData', 'WayDataController@update')->name('updateWayData');
	Route::post('getAWBLocation', 'WayDataController@getAWBLocation')->name('getAWBLocation');
	Route::post('city_Search', 'OrderController@city_Search')->name('city_Search');
	Route::post('state_Search', 'OrderController@state_Search')->name('state_Search');
	Route::post('status_Search', 'OrderController@status_Search')->name('status_Search');
	Route::post('add_Transaction_Data','AddTransactionController@store')->name('add_Transaction_Data');
	Route::get('view_Transaction','AddTransactionController@show')->name('view_Transaction');
	Route::post('updateStatusWP', 'JsonController@getUpdateStatus')->name('updateStatusWP');
	Route::get('getAWBStatus/{awb}', 'JsonController@getAWBStatus')->name('getAWBStatus');
	Route::get('cronOrderStatusUpdate/{vid}', 'JsonController@cronOrderStatusUpdate')->name('cronOrderStatusUpdate');
	
}); 

