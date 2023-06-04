<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppDataController extends Controller
{
    public function FetchAllRecords(Request $request)
    {

        try {
            $TableName = $request->TableName;

            $recs = DB::table($TableName)->get();

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

            $recs = DB::table($TableName)->where('id', $id)->get();

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
                ->select('D.id', 'D.DistrictName', 'P.ProvinceName', 'D.DistrictID')
                ->get();

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
                ->select('C.id', 'D.DistrictName', 'C.ConstituencyName', 'C.ConstituencyID')
                ->get();

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
            $recs            = DB::table('provinces')->select(array_diff(\Schema::getColumnListing('provinces'), $excludedColumns))->get();

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
                ->get();

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
                ->get();

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
                ->select('G.id', 'G.ChvGroupID', 'G.ChvGroupName', 'V.VillageName')
                ->get();

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
                ->get();

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
                ->get();

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
                ->get();

            return response()->json(['records' => $recs]);

        } catch (Exception $e) {

            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

}
