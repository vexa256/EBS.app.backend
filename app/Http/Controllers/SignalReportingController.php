<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

use App\Models\ReportSignals;
use Illuminate\Support\Carbon;

use App\Http\Controllers\SystemJobsController;
use Illuminate\Support\Facades\Validator; // Correct namespace for Validator

class SignalReportingController extends Controller
{

    public function __construct()
    {

        $SystemJobsController = new SystemJobsController;
    }


    public function ReportCebsSignals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'UserID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $id = $request->id;
        $UserID = $request->UserID;

        $Signal = DB::table('ebs_signals')->where('id', $id)->first();

        $Rep = DB::table('users AS U')
            ->where('U.UserID',  $UserID)
            ->join('ebs_structures AS S', 'S.UserID', 'U.UserID')
            ->join('health_facilities AS H', 'H.HFID', 'S.FacilityID')
            ->join('wards AS W', 'W.WardID', 'H.WardID')
            ->leftJoin('chv_groups AS G', 'G.ChvGroupID', 'S.ChvGroupID')
            ->leftJoin('villages AS V', 'V.VillageID', 'G.VillageID')
            ->join('constituencies AS C', 'C.ConstituencyID', 'W.ConstituencyID')
            ->join('districts AS D', 'D.DistrictID', 'C.DistrictID')
            ->join('provinces AS P', 'P.ProvinceID', 'D.ProvinceID')
            ->select(
                'U.*',
                'S.*',
                'H.HFID',
                'W.WardID',
                'C.ConstituencyID',
                'D.DistrictID',
                'P.ProvinceID',
                'V.VillageID',
                'G.ChvGroupID',
            )
            ->first();


        date_default_timezone_set('Africa/Lusaka');

        // Check if the user has reported the same signal in the last 24 hours
        $existingUserReport = DB::table('report_signals')
            ->where('UserID', $UserID)
            ->where('SignalID', $Signal->SignalID)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if ($existingUserReport) {
            return response()->json(['error_a' => 'You have already reported this signal in the last 24 hours.']);
        }

        // Check if the same signal has been reported in the same ward in the last 24 hours
        $existingWardReport = DB::table('report_signals')
            ->where('VillageID', $Rep->VillageID)
            ->where('SignalID', $Signal->SignalID)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if ($existingWardReport) {
            return response()->json(['error_a' => 'This signal has already been reported in your village in the last 24 hours.']);
        }



        try {
            // Start transaction!
            DB::beginTransaction();

            // Generate the ReportID
            $ReportID = md5(uniqid() . $UserID . date('Y-m-d H:i:s'));

            // Insert into report_signals
            DB::table('report_signals')->insert(
                [
                    'ReportID'  => $ReportID,
                    'SignalID'  => $Signal->SignalID,
                    'EbsSignalCategoryID'  => $Signal->EbsSignalCategoryID,
                    'UserID'  => $UserID,
                    'EbsType'  => 'CEBS',
                    'SignalNumber'  => $Signal->SignalNumber,
                    'WardID'  => $Rep->WardID,
                    'ConstituencyID'  => $Rep->ConstituencyID,
                    'DistrictID'  => $Rep->DistrictID,
                    'ProvinceID'  => $Rep->ProvinceID,
                    'HealthFacilityID'  => $Rep->HFID,
                    'VillageID'  => $Rep->VillageID,
                    'ChvGroupID'  => $Rep->ChvGroupID,
                    'created_at'  => date('Y-m-d H:i:s'),
                ]
            );

            // Insert into triages
            DB::table('triages')->insert([
                'ReportID' => $ReportID,
                "created_at" => date('Y-m-d H:i:s'),
            ]);

            // Insert into verifications
            DB::table('verifications')->insert([
                'ReportID' => $ReportID,
                "created_at" => date('Y-m-d H:i:s'),
            ]);

            // Insert into risk_assements
            DB::table('risk_assements')->insert([
                'ReportID' => $ReportID,
                "created_at" => date('Y-m-d H:i:s'),
            ]);

            // Insert into responses
            DB::table('responses')->insert([
                'ReportID' => $ReportID,
                "created_at" => date('Y-m-d H:i:s'),
            ]);

            // If we reach this point, it means that no exceptions were thrown
            // i.e. no query has failed, and we can commit the transaction
            DB::commit();

            // After commit return success response
            return response()->json(['success' => 'Records inserted successfully']);
        } catch (\Exception $e) {
            // An error occured cancel the transaction...
            DB::rollback();

            // and return the exception message in json response.
            return response()->json(['error_a' => $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Signal reported successfully.']);
    }



    public function fetchCebsMyReportedSignals(Request $request)
    {
        $recs = DB::table('ebs_signal_categories as c')
            ->join('ebs_signals as s', 's.EbsSignalCategoryID', '=', 'c.EbsSignalCategoryID')
            ->join('report_signals as r', 'r.SignalID', '=', 's.SignalID')
            ->join('health_facilities as H', 'H.HFID', '=', 'r.HealthFacilityID')
            ->join('wards as W', 'W.WardID', '=', 'r.WardID')
            ->join('users AS U', 'U.UserID', '=', 'r.UserID')
            ->join('ebs_structures AS S', 'S.UserID', '=', 'U.UserID')
            ->where('s.EbsType', 'CEBS')
            ->where('r.UserID', $request->UserID)
            ->select(
                's.EbsSignal',
                'c.EbsSignalCategory',
                'r.created_at AS DateReported',
                'r.id',
                'U.name AS ReportersName',
                'S.Email AS ReportersEmail',
                'H.HealthFacilityName AS FacilityAlerted',
                'W.WardName AS WardAffected'
            )
            ->orderBy('r.created_at', 'desc')
            ->get();

        return response()->json(['records' => $recs]);
    }


    function FetchCebsReportCount()
    {

        $recs = DB::table('ebs_signal_categories as c')
            ->join('ebs_signals as s', 's.EbsSignalCategoryID', '=', 'c.EbsSignalCategoryID')
            ->join('report_signals as r', 'r.SignalID', '=', 's.SignalID')
            ->join('health_facilities as H', 'H.HFID', '=', 'r.HealthFacilityID')
            ->join('wards as W', 'W.WardID', '=', 'r.WardID')
            ->join('users AS U', 'U.UserID', '=', 'r.UserID')
            ->join('ebs_structures AS S', 'S.UserID', '=', 'U.UserID')
            ->where('s.EbsType', 'CEBS')
            ->where('r.UserID', $request->UserID)
            ->select(
                's.EbsSignal',
                'c.EbsSignalCategory',
                'r.created_at AS DateReported',
                'r.id',
                'U.name AS ReportersName',
                'S.Email AS ReportersEmail',
                'H.HealthFacilityName AS FacilityAlerted',
                'W.WardName AS WardAffected'
            )
            ->orderBy('r.created_at', 'desc')
            ->count();

        return response()->json(['records' => $recs]);
    }


    public function deleteReportSignal(Request $request)
    {
        $request->validate(['id' => 'required']);

        $reportSignal = ReportSignals::find($request->id);

        if ($reportSignal) { // Change is here
            ReportSignals::where('ReportID', $reportSignal->ReportID)->delete();

            return response()->json(['status' => "true", 'message' => 'The signal and associated data have been deleted successfully.'], 200);
        } else {
            return response()->json(['error_a' => "true", 'message' => 'The signal has already been deleted'], 200);
        }
    }


    function CountedReportedSignal()
    {

        $count =  DB::table('report_signals')->count();

        return response()->json(['records' => $count]);
    }
}
