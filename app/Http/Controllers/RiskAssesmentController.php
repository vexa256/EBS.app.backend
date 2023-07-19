<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\SystemJobsController;

class RiskAssesmentController extends Controller
{


    public function __construct()
    {
        //  Create ebs_structures_view with complete data
        $ebs_structures_report = new SystemJobsController;
    }


    public function SelectPendingRiskAssessment(Request $request)
    {

        $a = DB::table('ebs_structures')->where('UserID', $request->UserID)->first();
        $UserID = $request->UserID;


        $Rep = DB::table('verifications AS V')
            ->select('*')
            ->join('report_signals AS R', 'V.ReportID', '=', 'R.ReportID')
            ->join('ebs_signals AS ES', 'ES.EbsSignalCategoryID', '=', 'R.EbsSignalCategoryID')
            ->join('ebs_signal_categories AS C', 'C.EbsSignalCategoryID', '=', 'R.EbsSignalCategoryID')
            ->join('cebs_risk_assessment_report AS T', 'T.ReportID', '=', 'T.ReportID')
            ->join('ebs_structures_report AS S', 'S.HFID', '=', 'T.HFID')
            ->join('ebs_structures AS E', 'E.FacilityID', '=', 'T.HFID')
            ->where('T.EbsType', '=', 'CEBS')
            ->where('T.TriagingStatus', '=', 'Triaged')
            ->where('T.RiskAssessmentStatus', '=', 'Not Assessed')
            ->where('E.UserID', '=', $UserID)
            ->where(function ($query) {
                $query->where('E.Role', 'LIKE', '%Facilitate Risk Assessment%')
                    ->orWhere('E.Role', 'LIKE', '%Risk Assessment%')
                    ->orWhere('E.Role', 'LIKE', '%Response%');
                // ->where('T.TriagingStatus', 'Triaged');
            })
            ->groupBy('V.RiskAssessmentStatus')
            ->get();




        return response()->json(['records' => $Rep]);
    }

    public function CebsAssessmentSignalStats(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        $HFID = $a->FacilityID;

        $today = now();
        $oneMonthAgo = now()->subDays(30);

        $UnAssessed = DB::table('cebs_risk_assessment_report AS CR')
            ->where('CR.UserID', $UserID)
            // ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Triaged')
            ->where('CR.RiskAssessmentStatus', 'Not Assessed')
            ->groupBy('CR.ReportID')
            ->get()
            // ->unique('ReportID')
            ->count();



        $Assessed = DB::table('cebs_risk_assessment_report AS CR')
            ->where('CR.UserID', $UserID)
            // ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Triaged')
            ->whereBetween('T.created_at', [$oneMonthAgo, $today])
            ->where('CR.RiskAssessmentStatus', 'Assessed')
            ->groupBy('CR.ReportID')
            ->get()
            // ->unique('ReportID')
            ->count();






        return response()->json(

            [

                'UnAssessed' => $UnAssessed,
                'Assessed' => $Assessed,


            ]


        );
    }



    public function FetchUnAssessedSignals(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        // if ($a->count() > 0) {
        $HFID = $a->FacilityID;

        $ignoredColumns = [
            "created_at",
            "updated_at",
            "SignalID",
            "EscalatesRoleID",
            "RoleID",
            "EscalatesToRoleID",
            "FacilityID",
            "VerifiedByUserID",
            "Name",
            "UserID",
            "TriagingStatus",
            "ReportsToLevel",
            "RiskAssessmentByUserID",
            "SignalNumber",
            "RiskAssessmentDate",
            "RiskAssessmentDetails",
            "RecommendedAction",
            "HFID",
            "name",
            "email",
            "email_verified_at",
            "Email",
            "Role",
            "WardID",
            "Constituency",
            "ChvGroupID",
            "remember_token",
            "VillageID",
            "HealthFacilityID",
            "EbsSignalCategoryID",
            "password",
            "role",
            "OfficialDesignation",
            "ProvinceID",
            "DistrictID",
            "DelayStatus",
            "AdministrativeLevel",
            "ConstituencyID",
            "PhoneNumber",
            "TriagingDate",
        ];

        $recs = DB::table('cebs_risk_assessment_report AS T')
            ->join('report_signals AS R', 'R.SignalID', 'T.SignalID')
            ->join('ebs_structures AS S', 'S.UserID', 'T.UserID')
            ->where('S.UserID', $UserID)
            ->join('users AS U', 'U.UserID', 'R.UserID')
            // ->join('cebs_risk_assessment_report AS V', 'V.ReportID', 'T.ReportID')
            ->where('T.RiskAssessmentStatus', 'Not Assessed')
            // ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Triaged')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->where(function ($query) {
                $query->where('S.Role', 'LIKE', '%Facilitate Risk Assessment%')
                    ->orWhere('S.Role', 'LIKE', '%Risk Assessment%')
                    ->orWhere('S.Role', 'LIKE', '%Response%');
                // ->where('T.TriagingStatus', 'Triaged');
            })
            ->groupBy('T.ReportID')
            ->get();



        // Unset ignored columns from each record
        foreach ($recs as $rec) {
            foreach ($ignoredColumns as $col) {
                unset($rec->$col);
            }
        }

        return response()->json(['records' => $recs]);
        // } else {

        //     return response()->json(['status' => "Empty Records"]);
        // }
    }



    public function FetchCebsRiskAssessmentSignal(Request $request)
    {
        $ReportID = $request->ReportID;

        // Get all column names from your table
        $columns = Schema::getColumnListing('cebs_risk_assessment_report');

        // Columns to exclude
        $excludedColumns = [
            "created_at",
            "updated_at",
            "TriagingStatus",
            "SignalID",
            "TriagingDate",
            "TriagingStatus",
            "RiskAssessmentByUserID",
            "RiskAssessmentDelayStatus",

            "Role",
            "RoleID",
            "EbsType",
            "EbsType",
            "OfficialDesignation",
            "AdministrativeLevel",
            "EscalatesToRoleID",
            "FacilityID",
            "ReportsToLevel",
            "ChvGroupID",
            "UserID",
            "VerifiedByUserID",
            "VerificationDelayStatus",
            "DelayStatus",
            "ReportID",
            "HFID",
            "name",
            "email",
            "Email",
            "VerificationStatus",
            "EbsSignalCategoryID",
        ];

        // Remove excluded column names from the array of all column names
        $selectedColumns = array_diff($columns, $excludedColumns);

        // Prefix each column name with 'V.' to specify table
        $selectedColumns = array_map(function ($columnName) {
            return 'V.' . $columnName;
        }, $selectedColumns);

        // Add required extra columns
        array_push($selectedColumns, 'U.name AS ReportedBy', 'U.email AS ReportersEmail', 'U.PhoneNumber AS ReportersPhone');

        // Get the records with selected columns only
        $recs = DB::table('cebs_risk_assessment_report AS V')
            ->join('report_signals AS R', 'R.ReportID', 'V.ReportID')
            ->join('users AS U', 'U.UserID', 'V.UserID')
            ->where('V.ReportID', $ReportID)
            ->select($selectedColumns)
            ->groupBy('V.ReportID')
            ->get();

        return response()->json(['records' => $recs]);
    }

    public function RiskAssessEvent(Request $request)
    {

        $ReportID = $request->ReportID;

        DB::table($request->TableName)->where('ReportID', $ReportID)
            ->update($request->except(
                ['_token', 'id', 'TableName', 'PostRoute']
            ));


        return response()->json([
            [
                'status' => 'Signal Risk Assessment and Response Report Submitted',
            ],
        ], 200);
    }



    public function FetchCebsRiskAssessed(Request $request)
    {
        // $ReportID = $request->ReportID;

        // Get all column names from your table
        $columns = Schema::getColumnListing('cebs_risk_assessment_report');

        // Columns to exclude
        $excludedColumns = [
            "created_at",
            "updated_at",
            "TriagingStatus",
            "SignalID",
            "TriagingDate",
            "TriagingStatus",
            "RiskAssessmentByUserID",
            "RiskAssessmentDelayStatus",

            "Role",
            "RoleID",
            "EbsType",
            "EbsType",
            "OfficialDesignation",
            "AdministrativeLevel",
            "EscalatesToRoleID",
            "FacilityID",
            "ReportsToLevel",
            "ChvGroupID",
            "UserID",
            "VerifiedByUserID",
            "VerificationDelayStatus",
            "DelayStatus",
            "ReportID",
            "HFID",
            "name",
            "email",
            "Email",
            "VerificationStatus",
            "EbsSignalCategoryID",
        ];

        // Remove excluded column names from the array of all column names
        $selectedColumns = array_diff($columns, $excludedColumns);

        // Prefix each column name with 'V.' to specify table
        $selectedColumns = array_map(function ($columnName) {
            return 'V.' . $columnName;
        }, $selectedColumns);

        // Add required extra columns
        array_push($selectedColumns, 'U.name AS ReportedBy', 'U.email AS ReportersEmail', 'U.PhoneNumber AS ReportersPhone');

        // Get the records with selected columns only
        $recs = DB::table('cebs_risk_assessment_report AS V')
            ->join('report_signals AS R', 'R.ReportID', 'V.ReportID')
            ->join('users AS U', 'U.UserID', 'V.UserID')
            ->where('V.RiskAssessmentStatus', "Assessed")
            ->select($selectedColumns)
            ->groupBy('V.ReportID')
            ->get();

        return response()->json(['records' => $recs]);
    }
}
