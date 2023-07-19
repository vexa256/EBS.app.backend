<?php

//  ebs_structure_view_report logic

$UserID = $request->UserID;


$a = DB::table('ebs_structures AS S')
    ->join('users AS U', 'U.UserID', 'S.UserID')
    ->join('health_facilities AS H', 'H.HFID', 'S.FacilityID')
    ->join('wards AS W', 'W.WardID', 'H.WardID')
    ->join('villages AS V', 'V.WardID', 'W.WardID')
    ->join('constituencies AS C', 'C.WardID', 'W.WardID')
    ->join('districts AS D', 'D.DistrictID', 'C.DistrictID')
    ->join('provinces AS P', 'P.ProvinceID', 'D.ProvinceID')
    ->join('designations AS DS', 'DS.DesignationID ', 'S.OfficialDesignation')
    ->leftJoin('chv_groups AS G', 'G.ChvGroupID  ', 'S.ChvGroupID')
    ->select(

        'S.Role',
        'S.EbsType',
        'S.BriefDescription',
        'S.AdministrativeLevel',
        'S.Name',
        'S.PhoneNumber',
        'S.Email',
        'S.UserID',
        'S.id AS IdOfEbsStructures',
        'H.HFID',
        'H.HealthFacilityName',
        'W.WardID',
        'W.WardName',
        'V.VillageID',
        'V.VillageName',
        'C.ConstituencyID',
        'C.ConstituencyName',
        'D.DistrictName',
        'D.DistrictID',
        'P.ProvinceName',
        'P.ProvinceID',
        'DS.DesignationID',
        'DS.Designation',
        'G.ChvGroupID',
        'G.Designation',

    );
//  ebs_structure_view_report logic



// cebs verification record set 

  // $Rep = DB::table('verifications AS V')
        //     ->join('report_signals AS R', 'V.ReportID', '=', 'R.ReportID')
        //     ->join('ebs_signals AS ES', 'ES.EbsSignalCategoryID', '=', 'R.EbsSignalCategoryID')
        //     ->join('ebs_signal_categories AS C', 'C.EbsSignalCategoryID', '=', 'R.EbsSignalCategoryID')
        //     ->join('cebs_triage_report AS T', 'T.ReportID', '=', 'T.ReportID')
        //     ->where('T.EbsType', '=', 'CEBS')
        //     ->where('T.TriagingStatus', '=', 'Triaged')
        //     ->join('ebs_structures_report AS S', 'S.HFID', '=', 'T.HFID')
        //     ->join('ebs_structures AS E', 'E.FacilityID', '=', 'T.HFID')
        //     ->where('E.UserID', '=', $UserID)
        //     ->where(function ($query) {
        //         $query->where('E.Role', 'LIKE', '%Triaging and Verification%')
        //             ->orWhere('E.Role', 'LIKE', '%Verification%')
        //             ->orWhere('E.Role', 'LIKE', '%Triaging%')
        //             ->where('T.TriagingStatus', 'Triaged');
        //     })
        //     // ->select('V.VerificationStatus', '')
        //     ->get();