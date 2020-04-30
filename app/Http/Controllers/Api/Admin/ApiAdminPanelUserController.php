<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DepositRequestsResource;

// Models
use App\Model\TopupWalletRequest;
// use App\Model\UserDetails;
use App\Model\UserAccount;
// use App\Model\UserAchievements;
// use App\Model\UserTransactionHistory;
use App\User;
// use App\Model\WithDrawalRequest;

// Resources
// Simple User Related Resources
// use App\Http\Resources\UserResource;
// use App\Http\Resources\UserAccountResource;
// use App\Http\Resources\UserDetailsResource;
// use App\Http\Resources\SearchUserResource;
// use App\Http\Resources\UserProfileResource;
// use App\Http\Resources\UserTransactionHistoryResource;

// use Illuminate\Support\Facades\DB;

// AdminPanel Component Resources
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiAdminPanelUserController extends Controller
{
    public function getDepositRequests(Request $request)
    {
        // DB::enableQueryLog();
        // $log = DB::getQueryLog();
        $all_requests = TopupWalletRequest::with('user_details')->get();
        return response()->json(['data' => DepositRequestsResource::collection($all_requests)], 200);
    }

    public function approveDepositRequest(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'amount' => 'required'
        ]);

        $request_approved_at = Carbon::now();

        $result = TopupWalletRequest::where('id', $request->id)->update([
            'approved_by' => $request->user()->id,
            'approved_at' => $request_approved_at,
            'additional_note' => $request->additional_note,
            'status' => 'approved'
        ]);

        $user_data = User::where('id', $request->user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;
        $new_balance = $user_balance + $request->amount;

        UserAccount::where('user_id', $request->user_id)->update([
            'balance' => $new_balance
        ]);

        if ($result) {
            $data = [
                'note' => "Request approved",
                'date_time' => $request_approved_at
            ];
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['error' => "Error Occured!"], 500);
        }
    }

    public function rejectDepositRequest(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'amount' => 'required'
        ]);

        $request_rejected_at = Carbon::now();

        $result = TopupWalletRequest::where('id', $request->id)->update([
            'approved_by' => $request->user()->id,
            'approved_at' => $request_rejected_at,
            'additional_note' => $request->additional_note,
            'status' => 'rejected'
        ]);

        // $user_data = User::where('id', $request->user_id)->with('account')->first();

        if ($result) {
            $data = [
                'note' => "Request Rejected",
                'date_time' => $request_rejected_at
            ];
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['error' => "Error Occured!"], 500);
        }
    }
}
