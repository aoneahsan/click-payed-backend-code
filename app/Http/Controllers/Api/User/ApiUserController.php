<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\User;
use App\Model\UserAccount;
use App\Model\UserDetails;
use App\Model\WithDrawalRequest;
use App\Model\TopupWalletRequest;
use App\Model\UserTransactionHistory;

// Resources
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\SearchUserResource;
use App\Http\Resources\User\UserAccountResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Http\Resources\User\UserProfileResource;
use App\Http\Resources\User\UserTransactionHistoryResource;

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

    public function getUserProfileData(Request $request)
    {
        $user = User::where('id', $request->user()->id)->with('account')->with('details')->first();
        if ($user) {
            return response()->json(['data' => new UserProfileResource($user)], 200);
        } else {
            return response()->json(['error' => 'User Not Found!'], 500);
        }

    }

    public function updateUserProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'city' => 'required',
            'country' => 'required'
        ]);

        $result = User::where('id', $request->user()->id)->update([
            'name' => $request->name
        ]);

        $result = UserDetails::Where('user_id', $request->user()->id)->update([
            'city' => $request->city,
            'country' => $request->country
        ]);

        if ($result) {
            return response()->json(['data' => new UserResource($request->user())], 200);
        } else {
            return response()->json(['error' => $request], 500);
        }

    }

    public function getUserAccountData(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::where('id', $user_id)->with('account')->first();
        $user_account_details = $user_data['account'];
        return response()->json(['data' => new UserAccountResource($user_account_details)], 200);
    }

    public function getUserDetailsData(Request $request)
    {
        $user_id = $request->user()->id;
        $user_data = User::where('id', $user_id)->with('details')->first();
        $user_details = $user_data['details'];
        return response()->json(['data' => new UserDetailsResource($user_details)]);
    }

    public function searchPerson(Request $request)
    {
        $request->validate([
            'number' => 'required'
        ]);

        $user = User::where('phone_number', $request->number)->with('details')->with('account')->first();
        if (!$user) {
            return response()->json(['data' => 'User Not Found!'], 500);
        } else {
            return response()->json(['data' => new SearchUserResource($user)], 200);
        }

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

            $result = UserAccount::where('user_id', $user_id)->update([
                'coins' => $new_coins,
                'balance' => $new_balance
            ]);

            if ($result) {
                return response()->json(['data' => 'done'], 200);
            } else {
                return response()->json(['data' => 'error'], 500);
            }
        } else {
            return response()->json(['data' => 'error'], 500);
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

            $result = UserAccount::where('user_id', $user_id)->update([
                'coins' => $new_coins,
                'balance' => $new_balance
            ]);

            if ($result) {
                return response()->json(['data' => 'done'], 200);
            } else {
                return response()->json(['data' => 'error'], 500);
            }
        } else {
            return response()->json(['data' => 'error'], 500);
        }
    }

    public function transferCoinsRequest(Request $request)
    {
        $request->validate([
            'revicer_id' => 'required',
            'coins_to_transfer' => 'required'
        ]);

        $sender_user_id = $request->user()->id;
        $sender_user_data = User::where('id', $sender_user_id)->with('account')->first();
        $sender_user_coins = $sender_user_data['account']->coins;

        $reciver_user_data = User::where('id', $request->revicer_id)->with('account')->first();
        $reciver_user_coins = $reciver_user_data['account']->coins;

        if ($request->coins_to_transfer <= $sender_user_coins) {
            $sender_user_new_coins = $sender_user_coins - $request->coins_to_transfer;
            $reciver_user_new_coins = $reciver_user_coins + $request->coins_to_transfer;

            UserAccount::where('user_id', $sender_user_id)->update([
                'coins' => $sender_user_new_coins
            ]);

            $result = UserAccount::where('user_id', $request->revicer_id)->update([
                'coins' => $reciver_user_new_coins
            ]);

            if ($result) {
                return response()->json(['data' => 'done'], 200);
            } else {
                return response()->json(['error' => 'Error Occured Try Again!'], 500);
            }
        } else {
            return response()->json(['error' => 'Don`t Have Enough Coins'], 500);
        }
    }

    public function submitTopupWalletRequest(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'trx_id' => 'required',
            'amount' => 'required'
        ]);

        $user_id = $request->user()->id;
        $result = TopupWalletRequest::create([
            'user_id' => $user_id,
            'payment_method' => $request->payment_method,
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        if ($result) {
            return response()->json(['data' => 'Request Submited Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Error Occured Try Again!'], 500);
        }

    }

    public function submitWithdrawalRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required'
        ]);

        $user_id = $request->user()->id;

        $user_data = User::where('id', $user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;

        $new_user_balance = $user_balance - $request->amount;

        UserAccount::where('user_id', $user_id)->update([
            'balance' => $new_user_balance
        ]);

        $result = WithDrawalRequest::create([
            'user_id' => $user_id,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        if ($result) {
            return response()->json(['data' => 'Request Submited Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Error Occured Try Again!'], 500);
        }

    }

    public function getTransactionHistory(Request $request)
    {
        $records = UserTransactionHistory::where('user_id', $request->user()->id)->get();
        if ($records) {
            return response()->json(['data' => UserTransactionHistoryResource::collection($records)], 200);
        } else {
            return response()->json(['data' => 'No Records Found!'], 500);
        }
    }

    public function saveUserFirebaseToken(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $result = User::where('id', $request->user()->id)->update([
            'firebase_token' => $request->token
        ]);

        if ($result) {
            return response()->json(['data' => 'Done, Token Saved Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Error Occured While Saving Token!'], 500);
        }

    }

    public function deleteUserFirebaseToken(Request $request)
    {
        $result = User::where('id', $request->user()->id)->update([
            'firebase_token' => null
        ]);

        if ($result) {
            return response()->json(['data' => 'Done, Token Removed Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Error Occured While Removing Token!'], 500);
        }
    }
}
