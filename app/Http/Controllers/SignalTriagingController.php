<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;
use App\Http\Controllers\SystemJobsController;

class SignalTriagingController extends Controller
{


    public function __construct()
    {
        //  Create ebs_structures_view with complete data
        $ebs_structures_report = new SystemJobsController;
    }

    public function TriageCebsSignals(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        // if ($a->count() > 0) {
        $HFID = $a->FacilityID;

        $ignoredColumns = [
            "created_at",
            "updated_at",
            "SignalID",

            "UserID",
            "TriagingStatus",
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

        $recs = DB::table('cebs_triage_report AS T')->where('T.UserID', $UserID)
            ->join('report_signals AS R', 'R.SignalID', 'T.SignalID')
            ->join('users AS U', 'U.UserID', 'R.UserID')
            ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Not Triaged')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->groupBy('R.ReportID')
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

    public function CebsTriagedSignals(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        $HFID = $a->FacilityID;

        $ignoredColumns = [
            "created_at",
            "updated_at",
            "SignalID",

            "UserID",
            "TriagingStatus",
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

        $recs = DB::table('cebs_triage_report AS T')->where('T.UserID', $UserID)
            ->join('report_signals AS R', 'R.SignalID', 'T.SignalID')
            ->join('users AS U', 'U.UserID', 'R.UserID')
            ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Triaged')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->groupBy('R.ReportID')
            ->get();



        // Unset ignored columns from each record
        foreach ($recs as $rec) {
            foreach ($ignoredColumns as $col) {
                unset($rec->$col);
            }
        }

        return response()->json(['records' => $recs]);
    }

    public function CebsDiscardedSignals(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        $HFID = $a->FacilityID;

        $ignoredColumns = [
            "created_at",
            "updated_at",
            "SignalID",

            "UserID",
            "TriagingStatus",
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

        $recs = DB::table('cebs_triage_report AS T')->where('T.UserID', $UserID)
            ->join('report_signals AS R', 'R.SignalID', 'T.SignalID')
            ->join('users AS U', 'U.UserID', 'R.UserID')
            ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Discarded')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->groupBy('R.ReportID')
            ->get();



        // Unset ignored columns from each record
        foreach ($recs as $rec) {
            foreach ($ignoredColumns as $col) {
                unset($rec->$col);
            }
        }

        return response()->json(['records' => $recs]);
    }

    public function SignalStats(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        $HFID = $a->FacilityID;

        $today = now();
        $oneMonthAgo = now()->subDays(30);

        $UnTriaged = DB::table('cebs_triage_report AS CR')
            ->where('CR.UserID', $UserID)
            ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Not Triaged')
            ->whereBetween('T.created_at', [$oneMonthAgo, $today])
            ->get()
            ->unique('ReportID')
            ->count();



        $Triaged = DB::table('cebs_triage_report AS CR')
            ->where('CR.UserID', $UserID)
            ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Triaged')
            ->whereBetween('T.created_at', [$oneMonthAgo, $today])
            ->get()
            ->unique('ReportID')
            ->count();


        $Discarded = DB::table('cebs_triage_report AS CR')
            ->where('CR.UserID', $UserID)
            ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Discarded')
            ->whereBetween('T.created_at', [$oneMonthAgo, $today])
            ->get()
            ->unique('ReportID')
            ->count();



        return response()->json(

            [

                'UnTriaged' => $UnTriaged,
                'Triaged' => $Triaged,
                'Discarded' => $Discarded,

            ]


        );
    }


    public function TriageASignal(Request $request)
    {
        $UserID = $request->UserID;
        $ReportID = $request->ReportID;


        DB::table('triages')->where('ReportID', $ReportID)->update([

            "TriagingStatus" => "Triaged",
            "TriagedByUserID" => $UserID,
            "TriagingDate" => date('Y-m-d'),
        ]);


        return response()->json(

            ['status' => "Signal has been marked as triaged"]


        );
    }



    public function ReverseCebsSignalTriage(Request $request)
    {
        $UserID = $request->UserID;
        $ReportID = $request->ReportID;


        DB::table('triages')->where('ReportID', $ReportID)->update([

            "TriagingStatus" => "Not Triaged",
            "TriagedByUserID" => $UserID,
            "TriagingDate" => date('Y-m-d'),
        ]);


        return response()->json(

            ['status' => "Signal triage status reversed and the signal has been marked not triaged"]


        );
    }


    public function DiscardASignal(Request $request)
    {
        $UserID = $request->UserID;
        $ReportID = $request->ReportID;


        DB::table('triages')->where('ReportID', $ReportID)->update([

            "TriagingStatus" => "Discarded",
            "TriagedByUserID" => $UserID,
            "TriagingDate" => date('Y-m-d'),
        ]);


        return response()->json(

            ['status' => "Signal has been marked as discarded"]


        );
    }


    public function ReverseCebsDiscardedTriage(Request $request)
    {
        $UserID = $request->UserID;
        $ReportID = $request->ReportID;


        DB::table('triages')->where('ReportID', $ReportID)->update([

            "TriagingStatus" => "Not Triaged",
            "TriagedByUserID" => $UserID,
            "TriagingDate" => date('Y-m-d'),
        ]);


        return response()->json(

            ['status' => "Signal triage status reversed and the signal has been marked not triaged"]


        );
    }
}
