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
    /*//Route::apiResource('clients', 'ClientController');
    //Route::apiResource('otheruser', 'OtherUserController');
    //Route::apiResource('pets', 'PetController');
    //Route::apiResource('breed', 'BreedController');
    //Route::apiResource('service', 'ServiceController');
    //Route::apiResource('petcategory', 'PetCategoryController');
    //Route::apiResource('petcoat', 'PetCoatLevelController');
    Route::apiResource('petaggresive', 'PetAggresiveLevelController');
    Route::apiResource('classificationmonth', 'ClassificationMonthController');
    Route::apiResource('petclassification', 'PetClassificationController');
    Route::apiResource('petclass', 'PetClassController');
    Route::apiResource('country', 'CountryController');
    Route::apiResource('state', 'StateController');
    Route::apiResource('city', 'CityController');
    Route::apiResource('area', 'AreaController');
    Route::apiResource('zone', 'ZoneController');
    Route::apiResource('servicemode', 'ServiceModeController');
    Route::apiResource('servicecost', 'ServiceCostController');
    Route::apiResource('classificationcharge', 'ClassificationChargeController');
    Route::apiResource('timeslot', 'TimeSlotController');
    Route::apiResource('appointment', 'AppointmentController');
    Route::apiResource('vehicle', 'VehicleController');
    Route::post('getcost', [ServiceCostController::class, 'getcost'])->name('getcost');
    Route::post('appointmentSearch', 'AppointmentController@appointmentSearch')->name('appointmentSearch');
    Route::post('clientappointment', 'AppointmentController@clientappointment')->name('clientappointment');
    Route::post('clientpet', 'PetController@clientpet')->name('clientpet');
    Route::post('free_vehicle', 'AppointmentController@free_vehicle')->name('free_vehicle');
    Route::post('assign_appointment', 'AppointmentController@assign_appointment')->name('assign_appointment');
    Route::post('schedule_appointment', 'AppointmentController@schedule_appointment')->name('schedule_appointment');
    Route::post('cancel_appointment', 'AppointmentController@cancel_appointment')->name('cancel_appointment');
    Route::get('upcomingappointment', 'AppointmentController@upcomingappointment')->name('upcomingappointment');
    Route::get('groomerappointments', 'AppointmentController@groomerappointments')->name('groomerappointments');
   */
    Route::get('insertAllOrders',[App\Http\Controllers\OrderController::class,'insertAllOrders']);
    Route::get('getOrderdetail',[App\Http\Controllers\OrderController::class,'OrderDetail']);
    Route::get('getProductdetail',[App\Http\Controllers\ProductController::class,'productDetail']);
  Route::get('getDashbaord',[App\Http\Controllers\DashboardController::class,'Dashboard_Detail']);
    Route::get('walletdetail',[App\Http\Controllers\WalletController::class,'Wallet_detail']);
    Route::get('reportdetail',[App\Http\Controllers\ReportController::class,'Report_detail']);
    Route::get('Accountdetail',[App\Http\Controllers\AccountController::class,'Account_detail']);
     Route::get('get_orderProfile',[App\Http\Controllers\ReportController::class,'ReportProfile_detail']);
     
   
    //Route::get('getProductdetail123',[App\Http\Controllers\OrderController::class,'OrderDetail']);
  // Route::get('getjson',[App\Http\Controllers\JsonController::class,'getJson']);
   // 
    
}); 

