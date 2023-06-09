<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\FormEngine;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;
use App\Http\Controllers\AppDataController;
use App\Http\Controllers\MobileAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */
// Route::middleware('auth:api', 'throttle:6000000,1')->group(function () {

Route::controller(MobileAuthController::class)->group(function () {
    Route::post('AuthenticateUser', 'AuthenticateUser');
});
Route::controller(AppDataController::class)->group(function () {

    Route::post('FetchAllRecords', 'FetchAllRecords');
    Route::post('FetchSpecificRecords', 'FetchSpecificRecords');
    Route::get('/', 'NET');
    Route::any('FetchDistricts', 'FetchDistricts');
    Route::any('FetchProvinces', 'FetchProvinces');
    Route::any('FetchConstituencies', 'FetchConstituencies');
    Route::any('FetchWards', 'FetchWards');
    Route::any('FetchVillages', 'FetchVillages');
    Route::any('FetchChvGroups', 'FetchChvGroups');
    Route::any('FetchHealthFacilities', 'FetchHealthFacilities');
    Route::any('FetchVetFacilities', 'FetchVetFacilities');
    Route::any('FetchEnvFacilities', 'FetchEnvFacilities');
    // Route::any('FetchEbsStructures', 'FetchEbsStructures');
    Route::any('FetchDesignations', 'FetchDesignations');
    Route::post('CreateEBsStructure', 'CreateEBsStructure');
    Route::post('UpdateEBsStructure', 'UpdateEBsStructure');
    Route::any('FetchCebsStructures', 'FetchCebsStructures');
    Route::any('FetchVebsStructures', 'FetchVebsStructures');
    Route::any('fetchEebsStructures', 'fetchEebsStructures');
    Route::any('FetchMebsStructures', 'FetchMebsStructures');
    Route::any('FetchHotlineStructures', 'FetchHotlineStructures');
    Route::any('FetchEbsSignalCategory', 'FetchEbsSignalCategory');
    Route::any('FetchEbsSignals', 'FetchEbsSignals');
    Route::any('FetchHFEbsSignals', 'FetchHFEbsSignals');
    Route::any('FetchMEBsSignals', 'FetchMEBsSignals');
    Route::any('FetchHotlineSignals', 'FetchHotlineSignals');
    Route::any('FetchEEBSSignals', 'FetchEEBSSignals');
    // Route::any('FetchEebsStructures', 'FetchEebsStructures');

});

Route::controller(FormEngine::class)->group(function () {

    Route::any('getColumnDetails', 'getColumnDetails');

});

Route::controller(CrudController::class)->group(function () {

    Route::post('MassDelete', 'MassDelete')->name('MassDelete');

    Route::post('MassUpdate', 'MassUpdate')->name('MassUpdate');

    Route::post('MassInsert', 'MassInsert')->name('MassInsert');
});

Route::post('/log', function (Request $request) {
    $error = $request->all();

    // Check if required keys exist in the $error array
    $exception     = $error['exception'] ?? 'Unknown';
    $message       = $error['message'] ?? 'Unknown';
    $file          = $error['file'] ?? 'Unknown';
    $traceFile     = $error['trace'][0]['file'] ?? 'Unknown';
    $traceFunction = $error['trace'][0]['function'] ?? 'Unknown';

    // Format the log message
    $logMessage = sprintf(
        "exception '%s' with message '%s' in %s:\nStack trace:\n#0 %s:%s",
        $exception,
        $message,
        $file,
        $traceFile,
        $traceFunction
    );

    if (isset($error['serverError'])) {
        // If server error details were provided, add them to the log message
        $logMessage .= "\nServer error: " . $error['serverError'];
    }

    Log::error($logMessage);
});
