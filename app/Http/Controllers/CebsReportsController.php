<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class CebsReportsController extends Controller
{
    public function __construct()
    {

        // Update where RiskAssessmentDate is not null
        $affectedRows_w = DB::table('risk_assements')
            ->whereNotNull('RiskAssessmentDate')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, STR_TO_DATE(RiskAssessmentDate, \'%Y-%m-%d %H:%i:%s\')) >= 24')
            ->update(['DelayStatus' => 'Delayed']);

        // Update where RiskAssessmentDate is null and created_at is more than 24 hours old
        $affectedRows_w += DB::table('risk_assements')
            ->whereNull('RiskAssessmentDate')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24')
            ->update(['DelayStatus' => 'Delayed']);



        // Delayed Verification
        // Update where VerificationDate is not null
        $affectedRows = DB::table('verifications')
            ->whereNotNull('VerificationDate')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, STR_TO_DATE(VerificationDate, \'%Y-%m-%d %H:%i:%s\')) >= 24')
            ->update(['DelayStatus' => 'Delayed']);

        // Update where VerificationDate is null and created_at is more than 24 hours old
        $affectedRows += DB::table('verifications')
            ->whereNull('VerificationDate')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24')
            ->update(['DelayStatus' => 'Delayed']);
        // Delayed Triages
        // Update where TriagingDate is not null
        $Triages_a = DB::table('triages')
            ->whereNotNull('TriagingDate')
            ->whereRaw('DATEDIFF(DATE(created_at), DATE(TriagingDate)) >= 1')
            ->update(['DelayStatus' => 'Delayed']);

        // Update where TriagingDate is null and created_at is more than 24 hours old
        $Triages_a += DB::table('triages')
            ->whereNull('TriagingDate')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24')
            ->update(['DelayStatus' => 'Delayed']);
    }

    public function ReportingByDistrict()
    {

        $currentYear = date('Y');

        $result = DB::table('report_signals AS R')
            ->join('districts AS D', 'D.DistrictID', 'R.DistrictID')
            ->select('D.DistrictName', DB::raw('count(R.DistrictID) as SignalsReported'))
            ->whereYear('R.created_at', $currentYear)
            ->groupBy('D.DistrictName')
            ->get();

        return response()->json(['result' => $result]);
    }

    public function Top5ReportingByDistrict()
    {

        $currentYear = date('Y');
        $result = DB::table('report_signals AS R')
            ->join('districts AS D', 'D.DistrictID', 'R.DistrictID')
            ->select('D.DistrictName', DB::raw('count(R.DistrictID) as count'))
            ->groupBy('D.DistrictName')
            ->whereYear('R.created_at', $currentYear)
            ->orderByRaw('count(R.DistrictID) DESC')
            ->limit(5)
            ->get();

        return response()->json(['result' => $result]);
    }

    public function Least5ReportingByDistrict()
    {
        $currentYear = date('Y');
        $result = DB::table('report_signals AS R')
            ->join('districts AS D', 'D.DistrictID', 'R.DistrictID')
            ->select('D.DistrictName', DB::raw('count(R.DistrictID) as count'))
            ->groupBy('D.DistrictName')
            ->whereYear('R.created_at', $currentYear)
            ->orderByRaw('count(R.DistrictID) ASC')
            ->limit(5)
            ->get();

        return response()->json(['result' => $result]);
    }


    public function TimeResponseAnalytics()
    {
        // For verifications
        $verifications = DB::table('verifications')
            ->select(
                DB::raw('count(*) as total_records'),
                DB::raw('sum(case when DelayStatus = "Delayed" then 1 else 0 end) as delayed_records'),
                DB::raw('sum(case when DelayStatus != "Delayed" then 1 else 0 end) as ontime_records')
            )
            ->get();

        // For triages
        $triages = DB::table('triages')
            ->select(
                DB::raw('count(*) as total_records'),
                DB::raw('sum(case when DelayStatus = "Delayed" then 1 else 0 end) as delayed_records'),
                DB::raw('sum(case when DelayStatus != "Delayed" then 1 else 0 end) as ontime_records')
            )
            ->get();

        // For risk assessments
        $riskAssessments = DB::table('risk_assements')
            ->select(
                DB::raw('count(*) as total_records'),
                DB::raw('sum(case when DelayStatus = "Delayed" then 1 else 0 end) as delayed_records'),
                DB::raw('sum(case when DelayStatus != "Delayed" then 1 else 0 end) as ontime_records')
            )
            ->get();

        return response()->json([
            'verifications' => $verifications,
            'triages' => $triages,
            'risk_assessments' => $riskAssessments
        ]);
    }

    public function FetchRiskAssessmentCounts()
    {
        $currentYear = date('Y');

        $counts = [
            'Very Low' => DB::table('risk_assements')
                ->where('RecommendedAction', 'LIKE', '%VERY LOW%')
                ->whereYear('created_at', $currentYear)
                ->count(),

            'Low' => DB::table('risk_assements')
                ->where('RecommendedAction', 'LIKE', '%LOW%')
                ->whereYear('created_at', $currentYear)
                ->count(),

            'Moderate' => DB::table('risk_assements')
                ->where('RecommendedAction', 'LIKE', '%MODERATE%')
                ->whereYear('created_at', $currentYear)
                ->count(),

            'High' => DB::table('risk_assements')
                ->where('RecommendedAction', 'LIKE', '%HIGH%')
                ->whereYear('created_at', $currentYear)
                ->count(),

            'Very High' => DB::table('risk_assements')
                ->where('RecommendedAction', 'LIKE', '%VERY HIGH%')
                ->whereYear('created_at', $currentYear)
                ->count()
        ];

        return response()->json($counts);
    }
    public function ReportedVsVerified()
    {
        $currentYear = Carbon::now()->year;

        // Count all reported signals for the current year
        $totalReports = DB::table('verifications')
            ->whereYear('created_at', $currentYear)
            ->count();

        // Count only the verified signals for the current year
        $verifiedReports = DB::table('verifications')
            ->whereYear('created_at', $currentYear)
            ->whereIn('VerificationStatus', ['Verified'])
            ->count();

        // Package the results for use in your graph
        $result = [
            'Total Reported' => $totalReports,
            'Total Verified' => $verifiedReports,
        ];

        return response()->json($result);
    }


    public function ReportedVsDiscarded()
    {
        $currentYear = Carbon::now()->year;

        // Count all reported signals for the current year
        $totalReports = DB::table('verifications')
            ->whereYear('created_at', $currentYear)
            ->count();

        // Count only the verified signals for the current year
        $verifiedReports = DB::table('verifications')
            ->whereYear('created_at', $currentYear)
            ->whereIn('VerificationStatus', ['Discarded'])
            ->count();

        // Package the results for use in your graph
        $result = [
            'Total Reported' => $totalReports,
            'Total Discarded' => $verifiedReports,
        ];

        return response()->json($result);
    }


    public function ReportedVsUnverified()
    {
        $currentYear = Carbon::now()->year;

        // Count all reported signals for the current year
        $totalReports = DB::table('verifications')
            ->whereYear('created_at', $currentYear)
            ->count();

        // Count only the verified signals for the current year
        $verifiedReports = DB::table('verifications')
            ->whereYear('created_at', $currentYear)
            ->whereIn('VerificationStatus', ['Not Verified'])
            ->count();

        // Package the results for use in your graph
        $result = [
            'Total Reported' => $totalReports,
            'Total Not Verified' => $verifiedReports,
        ];

        return response()->json($result);
    }

    public function countAllRecords()
    {
        $tableAliases = [
            'chv_groups' => 'CHV Groups',
            'constituencies' => 'Constituencies',
            'designations' => 'Designations',
            'districts' => 'Districts',
            'environment_facilities' => 'Environment Facilities',
            'health_facilities' => 'Health Facilities',
            'provinces' => 'Provinces',
            'users' => 'Users',
            'vet_facilities' => 'Vet Facilities',
            'villages' => 'Villages',
            'wards' => 'Wards'
        ];

        $counts = [];

        foreach ($tableAliases as $table => $alias) {
            $counts[$alias] = DB::table($table)->count();
        }

        return response()->json($counts);
    }
}
