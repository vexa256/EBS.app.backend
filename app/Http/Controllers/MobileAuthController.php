<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\SystemJobsController;

class MobileAuthController extends Controller
{
    public function __construct()
    {
        //  Create ebs_structures_view with complete data
        $ebs_structures_report = new SystemJobsController;
    }

    
    public function AuthenticateUser(Request $request)
    {
        $credentials = $request->only('PhoneNumber', 'password');
    
        if (Auth::attempt($credentials)) {
            // Authentication passed
            $user = Auth::user();
            unset($user->password);

            $UserReport = DB::table('users AS U')
                ->where('U.UserID',  $user->UserID)
                ->join('ebs_structures AS S', 'S.UserID', 'U.UserID')
                ->leftJoin('chv_groups AS G', 'G.ChvGroupID', 'S.ChvGroupID')
                ->select('*')
                ->get();

            return response()->json(['message' => 'Authentication successful', 'user' => $UserReport,
               
            ], 200);
        } else {
            // Authentication failed
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }


    public function UserRole(Request $request)
    {
        $UserID = $request->UserID;

        $Role =   DB::table('ebs_structures')
            ->where('UserID', $UserID)
            ->select('Role')
            ->first();

        return response()->json([
            'Role' => $Role->Role,
        ]);
    }
    
}
