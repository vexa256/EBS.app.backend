<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppDataController extends Controller
{
    public function NET()
    {
        return response()->json(['true' => 'connected']);
    }


    public function FetchAllRecords(Request $request)
    {

        try {
            $TableName = $request->TableName;

            $recs = DB::table($TableName)->get()->unique('id');

            $data = ['records' => $recs];

            return json_encode($data);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchSpecificRecords(Request $request)
    {

        try {
            $TableName = $request->TableName;
            $id        = $request->id;

            $recs = DB::table($TableName)->where('id', $id)->get()->unique('id');

            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchDistricts(Request $request)
    {

        try {
            $recs = DB::table('districts AS D')
                ->join('provinces AS P', 'P.ProvinceID', 'D.ProvinceID')
            ->select(
                'D.id',
                'D.DistrictName',
                'P.ProvinceName',
                'D.DistrictID'
            )
                ->get()->unique('id');

            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchConstituencies(Request $request)
    {

        try {
            $recs = DB::table('districts AS D')
                ->join('constituencies AS C', 'C.DistrictID', 'D.DistrictID')
            ->select(
                'C.id',
                'D.DistrictName',
                'C.ConstituencyName',
                'C.ConstituencyID'
            )
                ->get()->unique('id');

            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchProvinces(Request $request)
    {

        try {

            $excludedColumns = $request->input('excludedColumns', []);


            $recs = DB::table('provinces')->select(array_diff(
                \Schema::getColumnListing('provinces'),
                $excludedColumns
            ))->get()->unique('id');


            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchWards(Request $request)
    {

        try {

            $recs = DB::table('constituencies AS C')
                ->join('wards AS W', 'W.ConstituencyID', 'C.ConstituencyID')
                ->select('W.id', 'W.WardName', 'C.ConstituencyName', 'WardID')
            ->get()->unique('id');

            return response()->json(['records' => $recs]);
        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchVillages(Request $request)
    {

        try {

            $recs = DB::table('villages AS V')
                ->join('wards AS W', 'W.WardID', 'V.WardID')
                ->select('V.id', 'V.VillageName', 'V.VillageID', 'W.WardName')
                ->get()->unique('id');

            return response()->json(['records' => $recs]);
        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchChvGroups(Request $request)
    {

        try {

            $recs = DB::table('chv_groups AS G')
                ->join('villages AS V', 'G.VillageID', 'V.VillageID')
                ->select(
                    'G.id',
                    'G.ChvGroupID',
                    'G.ChvGroupName',
                    'V.VillageName'
                )
                ->get()->unique('id');

            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchHealthFacilities(Request $request)
    {

        try {

            $recs = DB::table('health_facilities AS H')
                ->join('wards AS W', 'W.WardID', 'H.WardID')
                ->select('H.*', 'W.WardID', 'W.WardName')
                ->groupBy('H.HealthFacilityName')
                ->get()->unique('id');

            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchVetFacilities(Request $request)
    {

        try {

            $recs = DB::table('vet_facilities AS V')
                ->join('wards AS W', 'W.WardID', 'V.WardID')
                ->select('V.*', 'W.WardID', 'W.WardName')
                ->get()->unique('id');

            return response()->json(['records' => $recs]);


        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function FetchEnvFacilities(Request $request)
    {

        try {

            $recs = DB::table('environment_facilities AS V')
                ->join('wards AS W', 'W.WardID', 'V.WardID')
                ->select('V.*', 'W.WardID', 'W.WardName')
            ->get()->unique('id');

            return response()->json(['records' => $recs]);
        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    public function FetchCebsStructures()
    {
        $recs = DB::table('ebs_structures AS S')
        ->leftJoin('health_facilities AS HF', 'HF.HFID', 'S.FacilityID')
        ->where('S.EbsType', 'CEBS')
        ->select('S.*', 'HF.HealthFacilityName', 'HF.FacilityCategory', 'HF.AdministrativeStructureLevel AS Level')
        ->get()
            ->unique('id');


        return response()->json(['records' => $recs]);
    }


    public function FetchEebsStructures()
    {
        $recs = DB::table('ebs_structures AS S')
        ->leftJoin('health_facilities AS HF', 'HF.HFID', 'S.FacilityID')
        ->where('S.EbsType', 'EEBS')
        ->select('S.*', 'HF.HealthFacilityName', 'HF.FacilityCategory', 'HF.AdministrativeStructureLevel AS Level')
        ->get()
            ->unique('id');

        return response()->json(['records' => $recs]);
    }
    public function FetchHotlineStructures()
    {

        $recs = DB::table('ebs_structures AS S')
        ->leftJoin('health_facilities AS HF', 'HF.HFID', 'S.FacilityID')
        ->where('S.EbsType', 'HOTLINE')
        ->select('S.*', 'HF.HealthFacilityName', 'HF.FacilityCategory', 'HF.AdministrativeStructureLevel AS Level')
        ->get()
            ->unique('id');


        return response()->json(['records' => $recs]);
    }
    public function FetchMebsStructures()
    {
        $recs = DB::table('ebs_structures AS S')
        ->leftJoin('health_facilities AS HF', 'HF.HFID', 'S.FacilityID')
        ->where('S.EbsType', 'MEBS')
        ->select('S.*', 'HF.HealthFacilityName', 'HF.FacilityCategory', 'HF.AdministrativeStructureLevel AS Level')
        ->get()
            ->unique('id');


        return response()->json(['records' => $recs]);
    }

    public function FetchVebsStructures()
    {
        $recs = DB::table('ebs_structures AS S')
        ->leftJoin('vet_facilities AS VF', 'VF.VFID', 'S.FacilityID')
        ->where('S.EbsType', 'VEBS')
        ->select('S.*', 'VF.VetFacilityName', 'VF.FacilityCategory AS VetFacilityCategory')
        ->get()
            ->unique('id');


        return response()->json(['records' => $recs]);
    }

  


    public function FetchDesignations()
    {
        try {

            $recs = DB::table('designations')->get()->unique('id');

            return response()->json(['records' => $recs]);
        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function CreateEBsStructure(Request $request)
    {



        try {

            DB::transaction(function () use ($request) {
                DB::table('users')->insert([
                    "name" => $request->Name,
                    "email" => $request->Email,
                    "password" => \Hash::make($request->PhoneNumber),
                    "PhoneNumber" => $request->PhoneNumber,
                    "UserID" => $request->UserID,
                    "role" => $request->AdministrativeLevel,
                ]);

                DB::table($request->TableName)
                    ->insert($request->except(['_token', 'id', 'TableName', 'PostRoute']));
            }, 5);

            return response()->json([
                [
                    'status' => 'The EBS structure and user account have been created successfully. The username and password for the new account is the phone number assigned.',
                ],

            ], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                [
                    'error_a' => 'Failed to insert data.  ' . $e->getMessage(),
                ],
            ], 422);
        }
    }

    public function UpdateEBsStructure(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $userData = [];
                if ($request->filled('Name')) {
                    $userData['name'] = $request->Name;
                }
                if ($request->filled('Email')) {
                    $userData['email'] = $request->Email;
                }
                if ($request->filled('PhoneNumber')) {
                    $userData['password'] = \Hash::make($request->PhoneNumber);
                }
                if ($request->filled('AdministrativeLevel')) {
                    $userData['role'] = $request->AdministrativeLevel;
                }
                if (!empty($userData)) {
                    DB::table('users')->where('UserID', $request->UserID)->update($userData);
                }

                $structureData = $request->except(['_token', 'id', 'TableName']);
                $structureData = array_filter($structureData, function ($value) {
                    return !is_null($value) && $value !== '';
                });
                if (!empty($structureData) && $request->filled('id')) {
                    DB::table($request->TableName)->where('id', $request->id)->update($structureData);
                }
            }, 5);
            return response()->json([
                [
                    'status' => 'The EBS structure and user account have been updated successfully.',
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'error' => 'Failed to update data. ' . $e->getMessage(),
            ], 422);
        }
    }


    public function FetchEbsSignalCategory()
    {
        $recs = DB::table('ebs_signal_categories')->get()->unique('id');


        return response()->json(['records' => $recs]);
    }

    public function FetchEbsSignals()
    {
        $recs = DB::table('ebs_signal_categories AS C')
            ->join('ebs_signals AS S', 'S.EbsSignalCategoryID', 'C.EbsSignalCategoryID')
            ->where('S.EbsType', "CEBS")
            ->get()->unique('id');

        return response()->json(['records' => $recs]);
    }


    public function FetchHFEbsSignals()
    {

        $recs = DB::table('ebs_signal_categories AS C')
            ->join('ebs_signals AS S', 'S.EbsSignalCategoryID', 'C.EbsSignalCategoryID')
            ->where('S.EbsType', "HFEBS")
            ->get()->unique('id');

        return response()->json(['records' => $recs]);
    }

    public function FetchMEBsSignals()
    {

        $recs = DB::table('ebs_signal_categories AS C')
            ->join('ebs_signals AS S', 'S.EbsSignalCategoryID', 'C.EbsSignalCategoryID')
            ->where('S.EbsType', "MEBS")
            ->get()->unique('id');

        return response()->json(['records' => $recs]);
    }



    public function FetchHotlineSignals()
    {

        $recs = DB::table('ebs_signal_categories AS C')
            ->join('ebs_signals AS S', 'S.EbsSignalCategoryID', 'C.EbsSignalCategoryID')
            ->where('S.EbsType', "HOTLINE")
            ->get()->unique('id');

        return response()->json(['records' => $recs]);
    }


    public function FetchEEBSSignals()
    {

        $recs = DB::table('ebs_signal_categories AS C')
            ->join('ebs_signals AS S', 'S.EbsSignalCategoryID', 'C.EbsSignalCategoryID')
            ->where('S.EbsType', "EEBS")
            ->get()->unique('id');

        return response()->json(['records' => $recs]);
    }

}
