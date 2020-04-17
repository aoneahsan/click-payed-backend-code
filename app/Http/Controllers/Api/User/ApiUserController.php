<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Http\Resources\UserAccountResource;
use App\Http\Resources\UserDetailsResource;

class ApiUserController extends Controller
{
    public function getUserPermissions(Request $request)
    {
        $permissions = $request->user()->getAllPermissions();
        return response()->json(['data' => $permissions], 404);
    }

    public function checkUserHasPermission(Request $request)
    {
        $permissions = $request->user()->getAllPermissions();
        return response()->json(['data' => $permissions], 404);
    }

    public function getUserAccountData(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::where('id', $user_id)->with('account')->first();
        $user_account_details = $user_data['account'];
        return response()->json(['data' => new UserAccountResource($user_account_details)]);
    }

    public function getUserDetailsData(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::where('id', $user_id)->with('details')->first();
        $user_details = $user_data['details'];
        return response()->json(['data' => new UserDetailsResource($user_details)]);
    }
}
