<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\Request;

class SystemJobsController extends Controller
{
    public function __construct()
    {




        // Get all ReportIDs from report_signals
        $reportIDs = DB::table('report_signals')->pluck('ReportID');

        // List of other tables to check
        $otherTables = ['triages', 'responses', 'verifications', 'risk_assements'];

        // For each table, delete any records whose ReportID is not in report_signals
        foreach ($otherTables as $table) {
            DB::table($table)->whereNotIn('ReportID', $reportIDs)->delete();
        }






        //  Create ebs_structures_view with complete data

        // Get the database name from .env
        $databaseName = env('DB_DATABASE');

        // Check if the view already exists
        $views = DB::select("SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ? AND UPPER(TABLE_NAME) = 'EBS_STRUCTURES_REPORT'", [$databaseName]);

        // If the view does not exist, create it
        if (empty($views)) {
            DB::statement("
            CREATE OR REPLACE VIEW ebs_structures_report AS 
               SELECT
                   S.Role,
                   S.EbsType,
                   S.BriefDescription,
                   S.AdministrativeLevel,
                   S.Name,
                   S.PhoneNumber,
                   S.Email,
                   S.UserID,
                   S.id AS IdOfEbsStructures,
                   H.HFID,
                   H.HealthFacilityName,
                   W.WardID,
                   W.WardName,
                   V.VillageID,
                   V.VillageName,
                   C.ConstituencyID,
                   C.ConstituencyName,
                   D.DistrictName,
                   D.DistrictID,
                   P.ProvinceName,
                   P.ProvinceID,
                   DS.DesignationID,
                   DS.Designation,
                   G.ChvGroupID,
                   G.ChvGroupName
               FROM 
                   ebs_structures AS S
               JOIN users AS U ON U.UserID = S.UserID
               JOIN health_facilities AS H ON H.HFID = S.FacilityID
               JOIN wards AS W ON W.WardID = H.WardID
               JOIN villages AS V ON V.WardID = W.WardID
               JOIN constituencies AS C ON C.ConstituencyID = W.ConstituencyID
               JOIN districts AS D ON D.DistrictID = C.DistrictID
               JOIN provinces AS P ON P.ProvinceID = D.ProvinceID
               JOIN designations AS DS ON DS.DesignationID  = S.OfficialDesignation
               LEFT JOIN chv_groups AS G ON G.ChvGroupID = S.ChvGroupID
           ");
        }





        // CEBS Triage report view 

        $databaseName = env('DB_DATABASE');

        // Check if all tables exist
        $tables = ['ebs_structures_report', 'report_signals', 'triages', 'ebs_signal_categories', 'ebs_signals', 'users'];
        foreach ($tables as $table) {
            $exists = DB::select("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$databaseName, $table]);
            if (empty($exists)) {
                // throw new Exception("Table $table does not exist.");
            }
        }

        // Check if the view already exists
        $views = DB::select("SELECT * FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$databaseName, 'cebs_triage_report']);

        if (empty($views)) {
            // Create the view if it does not exist
            DB::statement("CREATE OR REPLACE VIEW `cebs_triage_report` AS
            SELECT
                s.EbsSignal,
                c.EbsSignalCategory,
                R.created_at,
                R.id,
                R.SignalID,
                R.EbsSignalCategoryID,
                R.EbsType,
                U.name,
                TR.Email,
                TR.HealthFacilityName,
                TR.HFID,
                TR.WardName,
                TR.VillageName,
                TR.UserID,
                TR.Role,
                T.TriagingStatus,
                T.ReportID,
                T.DelayStatus,
                T.TriagingDate
            FROM
                ebs_structures_report AS TR
                JOIN report_signals as R ON R.WardID = TR.WardID AND R.DistrictID = TR.DistrictID AND R.HealthFacilityID = TR.HFID
                JOIN triages AS T ON T.ReportID = R.ReportID
                JOIN ebs_signal_categories AS c ON c.EbsSignalCategoryID = R.EbsSignalCategoryID
                JOIN ebs_signals as s ON s.EbsSignalCategoryID = c.EbsSignalCategoryID
                JOIN users AS U ON U.UserID = R.UserID
            WHERE
                TR.EbsType = 'CEBS' AND
                (TR.role = 'Triaging' OR TR.role = 'Triaging and Verification')
            GROUP BY
                s.EbsSignal,
                c.EbsSignalCategory,
                R.created_at,
                R.id,
                R.SignalID,
                R.EbsSignalCategoryID,
                R.EbsType,
                U.name,
                TR.Email,
                TR.HealthFacilityName,
                TR.HFID,
                TR.WardName,
                TR.VillageName,
                TR.UserID,
                TR.Role,
                T.TriagingStatus,
                T.ReportID,
                T.DelayStatus,
                T.TriagingDate
            ");


            DB::statement("CREATE OR REPLACE VIEW `cebs_verification_report` AS
            SELECT
                s.EbsSignal,
                c.EbsSignalCategory,
                R.created_at,
                R.id,
                R.SignalID,
                R.EbsSignalCategoryID,
                R.EbsType,
                U.name,
                TR.Email,
                TR.HealthFacilityName,
                TR.HFID,
                TR.WardName,
                TR.VillageName,
                TR.UserID,
                TR.Role,
                T.TriagingStatus,
                T.ReportID,
                T.DelayStatus,
                T.TriagingDate,
                V.VerificationStatus,
                V.DelayStatus AS VerificationDelayStatus,
                V.VerificationDate,
                V.VerifiedByUserID,
                V.created_at AS DateReported
            FROM
                ebs_structures_report AS TR
                JOIN report_signals as R ON R.WardID = TR.WardID AND R.DistrictID = TR.DistrictID AND R.HealthFacilityID = TR.HFID
                JOIN triages AS T ON T.ReportID = R.ReportID
                JOIN ebs_signal_categories AS c ON c.EbsSignalCategoryID = R.EbsSignalCategoryID
                JOIN ebs_signals as s ON s.EbsSignalCategoryID = c.EbsSignalCategoryID
                JOIN users AS U ON U.UserID = R.UserID
                JOIN verifications AS V ON V.ReportID = T.ReportID
            WHERE
                TR.EbsType = 'CEBS' AND
                (TR.role = 'Triaging' OR TR.role = 'Triaging and Verification') AND 
                T.TriagingStatus = 'Triaged'
            GROUP BY
               s.EbsSignal,
                c.EbsSignalCategory,
                R.created_at,
                R.id,
                R.SignalID,
                R.EbsSignalCategoryID,
                R.EbsType,
                U.name,
                TR.Email,
                TR.HealthFacilityName,
                TR.HFID,
                TR.WardName,
                TR.VillageName,
                TR.UserID,
                TR.Role,
                T.TriagingStatus,
                T.ReportID,
                T.DelayStatus,
                T.TriagingDate,
                V.VerificationStatus,
                V.DelayStatus,
                V.VerificationDate,
                V.VerifiedByUserID,
                V.created_at
            ");


            DB::statement("CREATE OR REPLACE VIEW `cebs_risk_assessment_report` AS
            SELECT
                s.EbsSignal,
                c.EbsSignalCategory,
                R.created_at,
                R.id,
                R.SignalID,
                R.EbsSignalCategoryID,
                R.EbsType,
                U.name,
                TR.Email,
                TR.HealthFacilityName,
                TR.HFID,
                TR.WardName,
                TR.VillageName,
                TR.UserID,
                TR.Role,
                T.TriagingStatus,
                T.ReportID,
                T.DelayStatus,
                T.TriagingDate,
                V.RiskAssessmentStatus,
                V.DelayStatus AS RiskAssessmentDelayStatus,
                V.RiskAssessmentDate,
                V.RiskAssessmentByUserID,
                V.RiskAssessmentDetails,
                V.RecommendedAction,
                VR.VerificationStatus,
                  VR.VerificationDate,
                V.created_at AS DateReported
            FROM
                ebs_structures_report AS TR
                JOIN report_signals as R ON R.WardID = TR.WardID AND R.DistrictID = TR.DistrictID AND R.HealthFacilityID = TR.HFID
                JOIN triages AS T ON T.ReportID = R.ReportID
                JOIN ebs_signal_categories AS c ON c.EbsSignalCategoryID = R.EbsSignalCategoryID
                JOIN ebs_signals as s ON s.EbsSignalCategoryID = c.EbsSignalCategoryID
                JOIN users AS U ON U.UserID = R.UserID
                JOIN risk_assements AS V ON V.ReportID = T.ReportID
                JOIN cebs_verification_report AS VR ON VR.ReportID = T.ReportID
            WHERE
                TR.EbsType = 'CEBS' AND
                (TR.role = 'Facilitate Risk Assessment' OR TR.role = 'Risk Assessment' OR TR.role = 'Response') AND 
                T.TriagingStatus = 'Triaged'
            GROUP BY
               s.EbsSignal,
                c.EbsSignalCategory,
                R.created_at,
                R.id,
                R.SignalID,
                R.EbsSignalCategoryID,
                R.EbsType,
                U.name,
                TR.Email,
                TR.HealthFacilityName,
                TR.HFID,
                TR.WardName,
                TR.VillageName,
                TR.UserID,
                TR.Role,
                T.TriagingStatus,
                T.ReportID,
                T.DelayStatus,
                T.TriagingDate,
                V.RiskAssessmentStatus,
                V.DelayStatus,
                V.RiskAssessmentDate,
                V.RiskAssessmentByUserID,
                V.RiskAssessmentDetails,
                VR.VerificationStatus,
                VR.VerificationDate,
                V.RecommendedAction,
                V.created_at
            ");
        }
    }
}
