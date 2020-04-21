<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\User;
use App\Model\UserAccount;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserAccountResource;
use App\Http\Resources\UserDetailsResource;
use App\Http\Resources\SearchUserResource;

use Illuminate\Http\Request;

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

    public function buyCoinsRequest(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::where('id', $user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;
        $user_coins = $user_data['account']->coins;

        if ($request->amount <= $user_balance) {

            $new_coins = $user_coins + ($request->amount * 10);
            $new_balance = $user_balance - $request->amount;

            $result = UserAccount::where('id', $user_id)->update([
                'coins' => $new_coins,
                'balance' => $new_balance,
            ]);

            if ($result) {
                return response()->json(['data' => 'done'], 200);
            } else {
                return response()->json(['data' => 'error'], 400);
            }
        } else {
            return response()->json(['data' => 'error'], 400);
        }

    }

    public function redeemCoinsRequest(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::where('id', $user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;
        $user_coins = $user_data['account']->coins;

        if ($request->number_of_coins <= $user_coins) {

            $new_coins = $user_coins - $request->number_of_coins;
            $new_balance = $user_balance + ($request->number_of_coins / 10);

            $result = UserAccount::where('id', $user_id)->update([
                'coins' => $new_coins,
                'balance' => $new_balance,
            ]);

            if ($result) {
                return response()->json(['data' => 'done'], 200);
            } else {
                return response()->json(['data' => 'error'], 400);
            }
        } else {
            return response()->json(['data' => 'error'], 400);
        }
    }

    public function searchPerson(Request $request)
    {
        $request->validate([
            'number' => 'required'
        ]);

        $user = User::where('phone_number', $request->number)->first();
        if (!$user) {
            return response()->json(['data' => 'User Not Found!'], 500);
        } else {
            return response()->json(['data' => new SearchUserResource($user)], 200);
        }
        
    }
}
