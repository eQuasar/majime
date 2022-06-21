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
	Route::post('walletDetail', 'WalletController@walletDetail')->name('walletDetail'); 
	Route::get('reportdetail',[App\Http\Controllers\ReportController::class,'Report_detail']);
	Route::get('Accountdetail',[App\Http\Controllers\AccountController::class,'Account_detail']);
	Route::get('get_orderProfile',[App\Http\Controllers\ReportController::class,'ReportProfile_detail']);
	//Route::get('order_Search',[App\Http\Controllers\OrderController::class,'Order_Search']);
	Route::post('order_Search', 'OrderController@Order_Search')->name('Order_Search');
	Route::post('product_Order_Search', 'ProductController@product_Order_Search')->name('product_Order_Search');
	Route::post('status_details', 'ReportController@status_details')->name('status_details');
	Route::get('insertAllOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
	 Route::get('order_Profile/{oid}',[App\Http\Controllers\OrderController::class,'OrdersProfile']);
    Route::get('order_Profile/{oid}', 'OrderController@order_Profile')->name('order_Profile');
    // Route::get('order_Profile/{oid}', 'OrderController@order_Profile')->name('order_Profile');
	Route::get('product_Profile/{variation_id}', 'ProductController@product_Profile')->name('product_Profile');
	Route::get('product_items/{variation_id}', 'ProductController@product_items')->name('product_items');
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
	Route::post('category_Search', 'ProductController@category_Search')->name('category_Search');
	Route::post('product_search', 'ProductController@product_Search')->name('product_Search');
	Route::post('color_Search', 'ProductController@color_Search')->name('color_Search');
    Route::post('add_Transaction_Data','AddTransactionController@store')->name('add_Transaction_Data');
	Route::get('view_Transaction','AddTransactionController@show')->name('view_Transaction');
	Route::post('updateStatusWP', 'JsonController@getUpdateStatus')->name('updateStatusWP');
	Route::get('getAWBStatus/{awb}', 'JsonController@getAWBStatus')->name('getAWBStatus');
	Route::get('cronOrderStatusUpdate/{vid}', 'JsonController@cronOrderStatusUpdate')->name('cronOrderStatusUpdate');
	// Route::post('get_Packdetail_Refund/{vid}','orderController@get_Packdetail_Refund')->name('get_Packdetail_Refund'); 
	Route::post('PackingRefund_changeStatus', 'OrderController@PackingRefund_changeStatus')->name('PackingRefund_changeStatus');
	Route::post('Refundchange_Status', 'OrderController@Refundchange_Status')->name('Refundchange_Status');
	Route::post('listOrder_Status', 'OrderController@listOrder_Status')->name('listOrder_Status');
	Route::post('download_Sheet', 'OrderController@download_Sheet')->name('download_Sheet');
    //Route::get('export', [OrderController::class, 'export']);
    Route::post('state_Search_Select', 'OrderController@state_Search_Select')->name('state_Search_Select');
    Route::get('state_data', 'OrderController@state_data')->name('state_data');
    Route::get('city_data', 'OrderController@city_data')->name('city_data');
    Route::get('status_data', 'OrderController@status_data')->name('status_data');
    Route::get('get_packdetail_Refund/{vid}','OrderController@get_packdetail_Refund')->name('get_packdetail_Refund');
    Route::get('product_data','ProductController@product_data')->name('product_data');
    Route::get('order_items/{variation_id}',[App\Http\Controllers\OrderController::class,'order_items']);
    Route::post('getDelivery_Details','ProductController@getDelivery_Details')->name('getDelivery_Details');
    Route::post('changeProcessing_Status', 'OrderController@changeProcessing_Status')->name('changeProcessing_Status');
    Route::post('getProcessingOrder_Details', 'OrderController@getProcessingOrder_Details')->name('getProcessingOrder_Details');
    Route::get('get_processing_data/{vid}/{status}','OrderController@get_processing_data')->name('get_processing_data');
    Route::post('product_Sheet_download', 'ProductController@product_Sheet_download')->name('product_Sheet_download');
    Route::post('processing_download_Sheet', 'OrderController@processing_download_Sheet')->name('processing_download_Sheet');
    Route::post('confirm_download_Sheet', 'OrderController@confirm_download_Sheet')->name('confirm_download_Sheet');
    Route::post('pending_download_Sheet', 'OrderController@pending_download_Sheet')->name('pending_download_Sheet');
    Route::post('delivery_download_Sheet', 'OrderController@delivery_download_Sheet')->name('delivery_download_Sheet');
    Route::post('onhold_download_Sheet', 'OrderController@onhold_download_Sheet')->name('onhold_download_Sheet');
}); 

