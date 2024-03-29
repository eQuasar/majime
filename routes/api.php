
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
	Route::get('insertAllgetOrderDetailsOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
	
    Route::get('my_orders', 'OrderController@my_orders')->name('my_orders');
	Route::get('smsTrigger', 'JsonController@smsTrigger')->name('smsTrigger');
    Route::post('getOrderDetails', 'OrderController@getOrderDetails')->name('getOrderDetails');
	Route::post('getProductdetail',[App\Http\Controllers\ProductController::class,'productDetail']);
	Route::post('getProductSaledetail',[App\Http\Controllers\ProductController::class,'productSaleDetail']);
	Route::get('getDashbaord',[App\Http\Controllers\DashboardController::class,'Dashboard_Detail']);
	Route::post('walletDetail', 'WalletprocessedController@show')->name('walletDetail'); 
	Route::get('reportdetail',[App\Http\Controllers\ReportController::class,'Report_detail']);
	Route::get('Accountdetail',[App\Http\Controllers\AccountController::class,'Account_detail']);
	Route::get('get_orderProfile',[App\Http\Controllers\ReportController::class,'ReportProfile_detail']);
	//Route::get('order_Search',[App\Http\Controllers\OrderController::class,'Order_Search']);
	Route::post('order_Search', 'OrderController@Order_Search')->name('Order_Search');
	Route::post('wallet_Search', 'OrderController@wallet_Search')->name('wallet_Search');
	Route::post('change_status_on_dispatch', 'OrderController@changeStatusDispatch')->name('change_status_on_dispatch');
	Route::post('product_Order_Search', 'ProductController@product_Order_Search')->name('product_Order_Search');
	Route::post('status_details', 'ReportController@status_details')->name('status_details');
	// Route::get('insertAllOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
	 Route::get('order_Profile/{oid}',[App\Http\Controllers\OrderController::class,'OrdersProfile']);
    Route::get('order_Profile/{oid}', 'OrderController@order_Profile')->name('order_Profile');
    // Route::get('order_Profile/{oid}', 'OrderController@order_Profile')->name('order_Profile');
	Route::get('product_Profile/{product_id}', 'ProductController@product_Profile')->name('product_Profile');
	Route::get('product_items/{product_id}', 'ProductController@product_items')->name('product_items');
	Route::get('product_detail/{product_id}', 'ProductController@product_detail')->name('product_detail');
	Route::post('category', 'ProductController@category')->name('category');
	Route::get('get_order_data', 'JsonController@get_order_data')->name('get_order_data');
	Route::get('getOrderOnStatus/{vid}/{status}', 'OrderController@getOrderOnStatus')->name('getOrderOnStatus');
	Route::get('getComplete_OrdersStatus/{vid}/{statrto}/{statdto}/{statcomp}/{clos}', 'OrderController@getComplete_OrdersStatus')->name('getComplete_OrdersStatus');
	Route::get('getpackdetail/{vid}', 'OrderController@getPackdetail')->name('getpackdetail'); 
	// Route::get('getConfirmeddetail/{vid}', 'OrderController@getConfirmeddetail')->name('getConfirmeddetail');
	

     Route::post('getOrderdetail',[App\Http\Controllers\OrderController::class,'OrderDetail']);
     // Route::post('getOrderdetail',[App\Http\Controllers\OrderController::class,'OrderDetail']);
    //Route::get('getProductdetail123',[App\Http\Controllers\OrderController::class,'OrderDetail']);
	
    Route::apiResource('vendors', 'VendorsController');
  	Route::get('getAllLinks',[App\Http\Controllers\JsonController::class,'getAllLinks']);
  	// Route::get('getJson',[App\Http\Controllers\JsonController::class,'getJson']);
	Route::post('getJson', 'JsonController@getJson')->name('getJson');
	Route::post('productgetJson', 'JsonController@productgetJson')->name('productgetJson');
	// Route::post('getJson', 'JsonController@getJson')->name('getJson');
	Route::post('changeStatus', 'OrderController@changeStatus')->name('changeStatus');
	Route::post('assignAWB', 'OrderController@assignAWB')->name('assignAWB');
	Route::post('return_awb', 'OrderController@return_awb')->name('return_awb');
	Route::post('assignAWBOrder', 'OrderController@assignAWBOrder')->name('assignAWBOrder');
	Route::get('return_order', 'OrderController@return_order')->name('return_order');
	Route::post('printSlip', 'OrderController@printSlip')->name('printSlip');
	Route::post('printOrderSlip', 'OrderController@printOrderSlip')->name('printOrderSlip');
	Route::post('getVid', 'AuthController@getVid')->name('getVid');
	Route::post('addWayData', 'WayDataController@getAWBLocation')->name('addWayData');
	Route::post('addWayData', 'WayDataController@store')->name('addWayData');

	Route::post('getawbdata', 'WayDataController@show')->name('getawbdata');
	Route::post('updateWayData', 'WayDataController@update')->name('updateWayData');
	Route::post('updateWayData', 'WayDataController@updatedata')->name('updateWayData');
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
    Route::post('city_data', 'OrderController@city_data')->name('city_data');
    Route::post('status_data', 'OrderController@status_data')->name('status_data');
    Route::get('get_packdetail_Refund/{vid}','OrderController@get_packdetail_Refund')->name('get_packdetail_Refund');
    Route::post('product_data','ProductController@product_data')->name('product_data');
    Route::get('order_items/{variation_id}',[App\Http\Controllers\OrderController::class,'order_items']);
    Route::post('getDelivery_Details','ProductController@getDelivery_Details')->name('getDelivery_Details');
    Route::post('changeProcessing_Status', 'OrderController@changeProcessing_Status')->name('changeProcessing_Status');
	Route::post('suborder_details', 'OrderController@suborder_details')->name('suborder_details');
	Route::post(' changeProcessing_Status_refund_amount', 'OrderController@ changeProcessing_Status_refund_amount')->name(' changeProcessing_Status_refund_amount');
    // Route::post('change_Processing_Status', 'OrderController@changeProcessing_Status_confirmed')->name('change_Processing_Status');
	Route::post('refund_amount', 'OrderController@refund_amount')->name('refund_amount');
    Route::post('getProcessingOrder_Details', 'OrderController@getProcessingOrder_Details')->name('getProcessingOrder_Details');
    Route::get('get_processing_data/{vid}/{status}','OrderController@get_processing_data')->name('get_processing_data');
    Route::post('product_Sheet_download', 'ProductController@product_Sheet_download')->name('product_Sheet_download');
    Route::post('processing_download_Sheet', 'OrderController@processing_download_Sheet')->name('processing_download_Sheet');
    Route::post('confirm_download_Sheet', 'OrderController@confirm_download_Sheet')->name('confirm_download_Sheet');
    Route::post('pending_download_Sheet', 'OrderController@pending_download_Sheet')->name('pending_download_Sheet');
    Route::post('delivery_download_Sheet', 'OrderController@delivery_download_Sheet')->name('delivery_download_Sheet');
    Route::post('onhold_download_Sheet', 'OrderController@onhold_download_Sheet')->name('onhold_download_Sheet');
    Route::get('get_Status/{vid}/{status}','ReturnController@get_Status')->name('get_Status');
	Route::post('addZone_Deatil','ZonedetailController@store')->name('addZone_Deatil');
	Route::get('get_zone','ZonedetailController@show')->name('get_zone');
	Route::get('zone_Search', 'OrderController@zone_Search')->name('zone_Search');
	Route::post('zonerate_card','ZoneratecardController@store')->name('zonerate_card');
	Route::post('vendor_rate_card','VendorRatecardController@store')->name('vendor_rate_card');
	Route::post('getvendorinfo','ZoneratecardController@getvendorinfo')->name('getvendorinfo');

	// Route::post('assign_wallet', 'OrderController@assign_wallet')->name('assign_wallet');
	Route::post('assign_wallet', 'WalletprocessedController@store')->name('assign_wallet'); 
	Route::post('complete_download_sheet', 'WalletprocessedController@index')->name('complete_download_sheet');
	Route::get('zone_rate','ZoneratecardController@show')->name('zone_rate');
	Route::get('Vendor_rate','VendorRatecardController@show')->name('Vendor_rate');
	Route::get('pco/{vid}', 'OrderController@Complete_orders')->name('Complete_orders');
	// Route::get('filter_Search', 'OrderController@filter_Search')->name('filter_Search');
	Route::post('filter_Search', 'OrderController@filter_Search')->name('filter_Search');
	Route::get('stat/{vid}', 'ProductController@stat')->name('stat');
	Route::post('wallet_Sheet_download', 'OrderController@wallet_Sheet_download')->name('wallet_Sheet_download');
	Route::get('dashboard_detail/{vid}', 'DashboardController@dashboard_detail')->name('dashboard_detail');
	Route::post('dashboard_search', 'DashboardController@dashboard_search')->name('dashboard_search');
	Route::get('dispatched_order_toclose/{vid}', 'ProductController@dispatched_order_toclose')->name('dispatched_order_toclose');
	Route::get('chart_data/{vid}', 'DashboardController@chart_data')->name('chart_data');
	Route::get('piechart_data/{vid}', 'DashboardController@piechart_data')->name('piechart_data');
	Route::get('secondpiechart_data/{vid}', 'DashboardController@secondpiechart_data')->name('secondpiechart_data');
	Route::get('getsales_data/{vid}', 'DashboardController@getsales_data')->name('getsales_data');
	Route::get('delpiechart_data/{vid}', 'DashboardController@delpiechart_data')->name('delpiechart_data');
	Route::get('getmargin_report/{vid}', 'DashboardController@getmargin_report')->name('getmargin_report');
	Route::get('getvedordata', 'DashboardController@getvedordata')->name('getvedordata');
	Route::post('getvedordata','VendorRatecardController@getvedordata')->name('getvedordata');
	Route::post('showzonedetail','ZoneratecardController@showzonedetail')->name('showzonedetail');
	Route::post('hsn_detail','HsnDetailController@store')->name('hsn_detail');
	Route::get('get_hsn','HsnDetailController@show')->name('get_hsn');
	Route::post('hsn_weight_update','HsnDetailController@update')->name('hsn_weight_update');
	Route::post('getProduct_data','HsnDetailController@getProduct_data')->name('getProduct_data');
	// Route::post('getJson', 'JsonController@getJson')->name('getJson');
	Route::post('insert_product/{vid}', 'JsonController@insert_product')->name('insert_product');
	Route::post('getorder_detail','JsonController@getorder_detail')->name('getorder_detail');
	Route::post('suborder_detail','JsonController@suborder_detail')->name('suborder_detail');
	Route::post('suborder_data','JsonController@suborder_data')->name('suborder_data');
	Route::post('getproduct_detail','ProductController@getproduct_detail')->name('getproduct_detail');
	Route::post('update_product_detail','ProductController@update_product_detail')->name('update_product_detail');
	Route::post('getProductVariation_detail','ProductController@getProductVariation_detail')->name('getProductVariation_detail');
	Route::post('update_productVariation_detail','ProductController@update_productVariation_detail')->name('update_productVariation_detail');
	Route::post('billing_detail','BillingController@billing_detail')->name('billing_detail');
	Route::post('get_category','ProductController@get_category')->name('get_category');
	Route::get('get_product_info','BillingController@get_product_info')->name('product_HsnWeight_export');
	// Route::get('import_product_info','BillingController@import_product_info')->name('import_product_info');
	Route::post('import_product_info','ProductController@import_product_info')->name('import_product_info');
	Route::post('product_insert','BillingController@product_insert')->name('product_insert');
	Route::post('billing_process','BillingController@billing_process')->name('billing_process');
	Route::post('return_billing_process','BillingController@return_billing_process')->name('return_billing_process');
	Route::get('billing_process','BillingController@billing_process')->name('billing_process');
	Route::post('get_import_data','ProductController@get_import_data')->name('get_import_data');
	Route::post('order_hencode_weight','HsnDetailController@order_hencode_weight')->name('order_hencode_weight');
	Route::post('sale_invoice_wise_detail','HsnDetailController@sale_invoice_wise_detail')->name('sale_invoice_wise_detail');
	Route::post('sale_return_wise_detail','HsnDetailController@sale_return_wise_detail')->name('sale_return_wise_detail');
	Route::post('state_wise_detail','HsnDetailController@state_wise_detail')->name('state_wise_detail');
	// Route::post('hsn_wise_detail','HsnDetailController@hsn_wise_detail')->name('hsn_wise_detail');
	Route::post('hsn_wise_detail_copy','BillingController@hsn_wise_detail_copy')->name('hsn_wise_detail_copy');
	Route::post('state_wise_detail_copy','OrderController@state_wise_detail_copy')->name('state_wise_detail_copy');
	Route::post('billing_filter','BillingController@billing_filter')->name('billing_filter');
	Route::post('pending_order','OrderController@pending_order')->name('pending_order');
	Route::get('data_scraping','AccountController@data_scraping')->name('data_scraping');
	Route::post('order_Searchdata', 'OrderController@order_Searchdata')->name('order_Searchdata');
	Route::post('order', 'OrderController@order')->name('order');
	Route::post('refundamount_update', 'OrderController@refundamount_update')->name('refundamount_update');
	Route::post('update_date', 'BillingController@update_date')->name('update_date');
	Route::post('sale_return_update_date', 'BillingController@sale_return_update_date')->name('sale_return_update_date');
	
}); 

