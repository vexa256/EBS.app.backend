<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SignalVerficationController extends Controller
{


    public function CebsSignalPendingVerification(Request $request)
    {
        $a = DB::table('ebs_structures')->where('UserID', $request->UserID)->first();
        $UserID = $request->UserID;


        $Rep = DB::table('verifications AS V')
            ->select('*')
            ->join('report_signals AS R', 'V.ReportID', '=', 'R.ReportID')
            ->join('ebs_signals AS ES', 'ES.EbsSignalCategoryID', '=', 'R.EbsSignalCategoryID')
            ->join('ebs_signal_categories AS C', 'C.EbsSignalCategoryID', '=', 'R.EbsSignalCategoryID')
            ->join('cebs_triage_report AS T', 'T.ReportID', '=', 'T.ReportID')
            ->join('ebs_structures_report AS S', 'S.HFID', '=', 'T.HFID')
            ->join('ebs_structures AS E', 'E.FacilityID', '=', 'T.HFID')
            ->where('T.EbsType', '=', 'CEBS')
            ->where('T.TriagingStatus', '=', 'Triaged')
            ->where('E.UserID', '=', $UserID)
            ->where(function ($query) {
                $query->where('E.Role', 'LIKE', '%Triaging and Verification%')
                    ->orWhere('E.Role', 'LIKE', '%Verification%')
                    ->orWhere('E.Role', 'LIKE', '%Triaging%')
                    ->where('T.TriagingStatus', 'Triaged');
            })
            ->groupBy('V.VerificationStatus')
            ->get();




        return response()->json(['records' => $Rep]);
    }

    public function CebsVerifySignalStats(Request $request)
    {
        $UserID = $request->UserID;

        $a = DB::table('ebs_structures')->where('UserID', $UserID)->first();

        $HFID = $a->FacilityID;

        $today = now();
        $oneMonthAgo = now()->subDays(30);

        $UnVerified = DB::table('cebs_verification_report AS CR')
            ->where('CR.UserID', $UserID)
            ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Triaged')
            ->where('CR.VerificationStatus', 'Not Verified')
            ->groupBy('CR.ReportID')
            ->get()
            // ->unique('ReportID')
            ->count();



        $Verified = DB::table('cebs_verification_report AS CR')
            ->where('CR.UserID', $UserID)
            ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Triaged')
            ->whereBetween('T.created_at', [$oneMonthAgo, $today])
            ->where('CR.VerificationStatus', 'Verified')
            ->groupBy('CR.ReportID')
            ->get()
            // ->unique('ReportID')
            ->count();


        $Discarded = DB::table('cebs_verification_report AS CR')
            ->where('CR.UserID', $UserID)
            ->where('CR.Role', 'Triaging')
            ->where('CR.EbsType', 'CEBS')
            ->where('CR.HFID', $HFID)
            ->join('report_signals AS RS', 'RS.ReportID', 'CR.ReportID')
            ->join('triages AS T', 'T.ReportID', 'T.ReportID')
            ->where('T.TriagingStatus', 'Discarded')
            ->whereBetween('T.created_at', [$oneMonthAgo, $today])
            ->where('CR.VerificationStatus', 'Discarded')
            ->groupBy('CR.ReportID')
            ->get()
            // ->unique('ReportID')
            ->count();



        return response()->json(

            [

                'UnVerified' => $UnVerified,
                'Verified' => $Verified,
                'Discarded' => $Discarded,

            ]


        );
    }


    public function VerifyCebsSignals(Request $request)
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
            ->join('ebs_structures AS S', 'S.UserID', 'T.UserID')
            ->join('users AS U', 'U.UserID', 'R.UserID')
            ->join('cebs_verification_report AS V', 'V.SignalID', 'T.SignalID')
            ->where('V.VerificationStatus', 'Not Verified')
            ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Triaged')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->where(function ($query) {
                $query->where('S.Role', 'LIKE', '%Triaging and Verification%')
                    ->orWhere('S.Role', 'LIKE', '%Verification%')
                    ->orWhere('S.Role', 'LIKE', '%Triaging%');
                // ->where('T.TriagingStatus', 'Triaged');
            })
            ->groupBy('V.ReportID')
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


    public function CebsFetchVerifySignal(Request $request)
    {
        $ReportID = $request->ReportID;

        // Get all column names from your table
        $columns = Schema::getColumnListing('cebs_verification_report');

        // Columns to exclude
        $excludedColumns = [
            "created_at",
            "updated_at",
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
        $recs = DB::table('cebs_verification_report AS V')
            ->join('report_signals AS R', 'R.ReportID', 'V.ReportID')
            ->join('users AS U', 'U.UserID', 'V.UserID')
            ->where('V.ReportID', $ReportID)
            ->select($selectedColumns)
            ->get();

        return response()->json(['records' => $recs]);
    }


    public function FetchCebsVerifyAttributes(Request $request)
    {
        $ReportID  = $request->ReportID;


        $recs = DB::table('report_signals')->where('ReportID', $ReportID)
            ->get();


        return response()->json(['records' => $recs]);
    }



    public function VerifyTheCebsSignal(Request $request)
    {

        $existingRecord = DB::table($request->TableName)
            ->where('ReportID', $request->ReportID)
            ->count();

        if ($existingRecord > 0) {
            // if a record exists, return a JSON response indicating that the signal has already been verified
            return response()->json([
                [
                    'status' => 'Signal Has Already Been Marked As Verified',
                ],
            ], 200);
        } else {

            DB::table($request->TableName)->insert(
                $request->except(
                    ['_token', 'id', 'TableName', 'PostRoute']
                )
            );

            DB::table('verifications')
                ->where('ReportID', $request->ReportID)
                ->update([

                    "VerificationStatus" => "Verified",
                    "VerificationDate" => date('Y-m-d'),
                    "VerifiedByUserID" => $request->VerifyingUserID,

                ]);


            return response()->json([
                [
                    'status' => 'Signal Marked As Verified Successfully',
                ],
            ], 200);
        }
    }


    public function CebsSignalVerificationReport(Request $request)
    {


        $ReportID  = $request->ReportID;

        // dd($ReportID);

        $recs = DB::table('cebs_verification_report AS C')

            ->join('verifications AS V', 'V.ReportID', 'C.ReportID')
            ->join('signal_verifications AS S', 'S.ReportID', 'C.ReportID')
            ->join('users AS U', 'U.UserID', 'V.VerifiedByUserID')
            ->where('C.ReportID', $ReportID)
            ->where('V.VerificationStatus', 'Verified')
            ->select(
                'U.name AS VerifiedBy',
                'S.WhatIsTheSignalSource',
                'S.ShortDescriptionOfTheSignal',
                'S.DateOfOccurrence',
                'S.TheEventIsAPublicHealthThreatTo',
                'S.SignalVerificationDate',
                'S.DateOfInformingTheNextLevelForAction',
                'S.DateOfVerification',
                'V.VerificationDate'
            )
            ->get();


        return response()->json([
            'records' => $recs,
            "ReportID" => $ReportID

        ]);
    }

    public function DiscardTheCebsSignal(Request $request)
    {

        DB::table('verifications')
            ->where('ReportID', $request->ReportID)
            ->update([

                "VerificationStatus" => "Discarded",
                "VerificationDate" => date('Y-m-d'),
                "VerifiedByUserID" => $request->VerifyingUserID,

            ]);


        return response()->json([
            [
                'status' => 'Signal Marked As Discarded Successfully',
            ],
        ], 200);
    }

    public function FetchVerifiedCebsSignals(Request $request)
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
            ->join('ebs_structures AS S', 'S.UserID', 'T.UserID')
            ->join('users AS U', 'U.UserID', 'R.UserID')
            ->join('cebs_verification_report AS V', 'V.SignalID', 'T.SignalID')
            ->where('V.VerificationStatus', 'Verified')
            ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Triaged')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->where(function ($query) {
                $query->where('S.Role', 'LIKE', '%Triaging and Verification%')
                    ->orWhere('S.Role', 'LIKE', '%Verification%')
                    ->orWhere('S.Role', 'LIKE', '%Triaging%');
                // ->where('T.TriagingStatus', 'Triaged');
            })
            ->groupBy('V.ReportID')
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

    public function FetchDiscardedCebsSignals(Request $request)
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
            ->join('ebs_structures AS S', 'S.UserID', 'T.UserID')
            ->join('users AS U', 'U.UserID', 'R.UserID')
            ->join('cebs_verification_report AS V', 'V.SignalID', 'T.SignalID')
            ->where('V.VerificationStatus', 'Discarded')
            ->where('T.Role', 'Triaging')
            ->where('T.EbsType', 'CEBS')
            ->where('T.HFID', $HFID)
            ->where('T.TriagingStatus', 'Triaged')
            ->select("*", "R.ReportID", "U.name AS ReportedBy", "U.Email AS ReportersEmail", 'U.PhoneNumber AS ReportersPhone')
            ->where(function ($query) {
                $query->where('S.Role', 'LIKE', '%Triaging and Verification%')
                    ->orWhere('S.Role', 'LIKE', '%Verification%')
                    ->orWhere('S.Role', 'LIKE', '%Triaging%');
                // ->where('T.TriagingStatus', 'Triaged');
            })
            ->groupBy('V.ReportID')
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
}
