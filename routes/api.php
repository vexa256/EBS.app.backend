<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\FormEngine;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;
use App\Http\Controllers\AppDataController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\CebsReportsController;
use App\Http\Controllers\RiskAssesmentController;
use App\Http\Controllers\SignalTriagingController;
use App\Http\Controllers\SignalReportingController;
use App\Http\Controllers\SignalVerficationController;


Route::controller(CebsReportsController::class)->group(function () {


    Route::any('countAllRecords', 'countAllRecords');
    Route::any('ReportedVsUnverified', 'ReportedVsUnverified');
    Route::any('ReportedVsDiscarded', 'ReportedVsDiscarded');
    Route::any('ReportedVsVerified', 'ReportedVsVerified');
    Route::any('FetchRiskAssessmentCounts', 'FetchRiskAssessmentCounts');
    Route::any('TimeResponseAnalytics', 'TimeResponseAnalytics');
    Route::any('Least5ReportingByDistrict', 'Least5ReportingByDistrict');
    Route::any('Top5ReportingByDistrict', 'Top5ReportingByDistrict');
    Route::any('ReportingByDistrict', 'ReportingByDistrict');
});




Route::controller(RiskAssesmentController::class)->group(function () {

    Route::any('FetchCebsRiskAssessed', 'FetchCebsRiskAssessed');
    Route::any('RiskAssessEvent', 'RiskAssessEvent');
    Route::any('FetchCebsRiskAssessmentSignal', 'FetchCebsRiskAssessmentSignal');
    Route::any('FetchUnAssessedSignals', 'FetchUnAssessedSignals');
    Route::any('CebsAssessmentSignalStats', 'CebsAssessmentSignalStats');
    Route::any('SelectPendingRiskAssessment', 'SelectPendingRiskAssessment');
});
Route::controller(SignalVerficationController::class)->group(function () {

    Route::any('FetchDiscardedCebsSignals', 'FetchDiscardedCebsSignals');

    Route::any('FetchVerifiedCebsSignals', 'FetchVerifiedCebsSignals');


    Route::any('DiscardTheCebsSignal', 'DiscardTheCebsSignal');

    Route::any('CebsSignalVerificationReport', 'CebsSignalVerificationReport');


    Route::any('VerifyTheCebsSignal', 'VerifyTheCebsSignal');

    Route::any('FetchCebsVerifyAttributes', 'FetchCebsVerifyAttributes');

    Route::any('CebsFetchVerifySignal', 'CebsFetchVerifySignal');

    Route::any('VerifyCebsSignals', 'VerifyCebsSignals');

    Route::any('CebsSignalPendingVerification', 'CebsSignalPendingVerification');
    Route::any('CebsVerifySignalStats', 'CebsVerifySignalStats');
});




Route::controller(SignalTriagingController::class)->group(function () {
    Route::any('SignalStats', 'SignalStats');
    Route::any('TriageCebsSignals', 'TriageCebsSignals');
    Route::any('TriageASignal', 'TriageASignal');
    Route::any('DiscardASignal', 'DiscardASignal');
    Route::any('CebsTriagedSignals', 'CebsTriagedSignals');
    Route::any('CebsDiscardedSignals', 'CebsDiscardedSignals');
    Route::any('ReverseCebsSignalTriage', 'ReverseCebsSignalTriage');
    Route::any('ReverseCebsDiscardedTriage', 'ReverseCebsDiscardedTriage');
});
Route::controller(SignalReportingController::class)->group(function () {

    Route::post('ReportCebsSignals', 'ReportCebsSignals');
    Route::post('fetchCebsMyReportedSignals', 'fetchCebsMyReportedSignals');
    Route::post('deleteReportSignal', 'deleteReportSignal');
    Route::post('FetchCebsReportCount', 'FetchCebsReportCount');
});


Route::controller(MobileAuthController::class)->group(function () {
    Route::post('UserRole', 'UserRole');
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
