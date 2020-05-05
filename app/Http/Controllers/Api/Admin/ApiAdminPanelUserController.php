<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DepositAccountsResource;
use App\Http\Resources\Admin\WithdrawalRequestsResource;

// Models
use App\User;
use App\Model\TopupWalletRequest;
use App\Model\UserAccount;
use App\Model\UserTransactionHistory;
use App\Model\WithDrawalRequest;
use App\Model\Admin\DepositAccount;

// Resources
use App\Http\Resources\Admin\DepositRequestsResource;

class ApiAdminPanelUserController extends Controller
{

    public function getAllDepositAccounts(Request $request)
    {
        $accounts = DepositAccount::all();
        return response()->json(['data' => DepositAccountsResource::collection($accounts)], 200);
    }

    public function addNewDepositAccount(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'account_number' => 'required'
        ]);
        
        $result = DepositAccount::create([
            'user_id' => $request->user()->id,
            'payment_method' => $request->payment_method,
            'account_number' => $request->account_number,
            'status' => 'active'
        ]);

        $accounts = DepositAccount::all();

        return response()->json(['data' => DepositAccountsResource::collection($accounts)], 200);

    }

    public function makeDepositRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'trx_id' => 'required'
        ]);

        $request_time = Carbon::now();

        $user_data = User::where('id', $request->user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;
        $new_balance = $user_balance + $request->amount;

        UserAccount::where('user_id', $request->user_id)->update([
            'balance' => $new_balance
        ]);

        if ($user_data->referer_user_id) {
            $referral_user_data = User::where('id', $user_data->referer_user_id)->with('account')->first();
            $referral_user_balance = $referral_user_data['account']->balance;
            $referral_user_new_balance = ($referral_user_balance + (($request->amount * 5)/100));
            UserAccount::where('user_id', $user_data->referer_user_id)->update([
                'balance' => $referral_user_new_balance
            ]);
        }

        $result = UserTransactionHistory::create([
            'user_id' => $request->user_id,
            'transaction_type' => 'direct_deposit_request',
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'remaining_balance' => $new_balance,
            'approved_by' => $request->user()->id
        ]);

        if ($result) {
            $data = [
                'note' => "Deposit Done!",
                'date_time' => $request_time
            ];
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['error' => "Error Occured!"], 500);
        }
    }

    public function getDepositRequests(Request $request)
    {
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

        if ($user_data->referer_user_id) {
            $referral_user_data = User::where('id', $user_data->referer_user_id)->with('account')->first();
            $referral_user_balance = $referral_user_data['account']->balance;
            $referral_user_new_balance = ($referral_user_balance + (($request->amount * 5)/100));
            UserAccount::where('user_id', $user_data->referer_user_id)->update([
                'balance' => $referral_user_new_balance
            ]);
        }

        UserTransactionHistory::create([
            'user_id' => $request->user_id,
            'topup_request_id' => $request->id,
            'transaction_type' => 'credit',
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'remaining_balance' => $new_balance,
            'approved_by' => $request->user()->id
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

    public function getAllWithdrawalRequests(Request $request)
    {
        $all_requests = WithDrawalRequest::with('user_details')->get();
        return response()->json(['data' => WithdrawalRequestsResource::collection($all_requests)], 200);
    }

    public function approveWithdrawalRequest(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'trx_id' => 'required'
        ]);

        $request_approved_at = Carbon::now();

        $result = WithDrawalRequest::where('id', $request->id)->update([
            'approved_by' => $request->user()->id,
            'approved_at' => $request_approved_at,
            'additional_note' => $request->additional_note,
            'status' => 'approved',
            'trx_id' => $request->trx_id
        ]);

        $user_data = User::where('id', $request->user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;
        $new_balance = $user_balance - $request->amount;

        UserAccount::where('user_id', $request->user_id)->update([
            'balance' => $new_balance
        ]);

        UserTransactionHistory::create([
            'user_id' => $request->user_id,
            'withdrawal_request_id' => $request->id,
            'transaction_type' => 'debit',
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'remaining_balance' => $new_balance,
            'approved_by' => $request->user()->id
        ]);

        if ($result) {
            $all_requests = WithDrawalRequest::with('user_details')->get();
            return response()->json(['data' => WithdrawalRequestsResource::collection($all_requests)], 200);
        } else {
            return response()->json(['error' => "Error Occured!"], 500);
        }
    }

    public function rejectWithdrawalRequest(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $request_rejected_at = Carbon::now();

        $result = WithDrawalRequest::where('id', $request->id)->update([
            'approved_by' => $request->user()->id,
            'approved_at' => $request_rejected_at,
            'additional_note' => $request->additional_note,
            'status' => 'rejected'
        ]);

        // $user_data = User::where('id', $request->user_id)->with('account')->first();

        if ($result) {
            $all_requests = WithDrawalRequest::with('user_details')->get();
            return response()->json(['data' => WithdrawalRequestsResource::collection($all_requests)], 200);
        } else {
            return response()->json(['error' => "Error Occured!"], 500);
        }
    }

    public function transferCoinsRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'trx_id' => 'required'
        ]);

        $request_time = Carbon::now();

        $user_data = User::where('id', $request->user_id)->with('account')->first();
        $user_balance = $user_data['account']->balance;
        $new_balance = $user_balance + $request->amount;

        UserAccount::where('user_id', $request->user_id)->update([
            'balance' => $new_balance
        ]);

        if ($user_data->referer_user_id) {
            $referral_user_data = User::where('id', $user_data->referer_user_id)->with('account')->first();
            $referral_user_balance = $referral_user_data['account']->balance;
            $referral_user_new_balance = ($referral_user_balance + (($request->amount * 5)/100));
            UserAccount::where('user_id', $user_data->referer_user_id)->update([
                'balance' => $referral_user_new_balance
            ]);
        }

        $result = UserTransactionHistory::create([
            'user_id' => $request->user_id,
            'transaction_type' => 'direct_deposit_request',
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'remaining_balance' => $new_balance,
            'approved_by' => $request->user()->id
        ]);

        if ($result) {
            $data = [
                'note' => "Deposit Done!",
                'date_time' => $request_time
            ];
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['error' => "Error Occured!"], 500);
        }
    }

    public function makeAddRemoveUserCoinsRequest(Request $request)
    {
        $request->validate([
            'is_mode_set_to_add_coins' => 'required',
            'number_of_coins' => 'required',
            'user_id' => 'required'
        ]);
        
        $user_data = User::where('id', $request->user_id)->with('account')->first();
        $user_coins = $user_data['account']->coins;
        if ($request->is_mode_set_to_add_coins) {
            $new_coins = $user_coins + $request->number_of_coins;
        } else if (!($request->is_mode_set_to_add_coins)) {
            if ($request->number_of_coins <= $user_coins) {
                $new_coins = $user_coins - $request->number_of_coins;
            } else {
                return response()->json(['error' => 'Error Occured, Request Failed!'], 500);
            }
        }
        else {
            return response()->json(['error' => 'Error Occured, Request Failed!'], 500);
        }
        
        $result = UserAccount::where('user_id', $request->user_id)->update([
            'coins' => $new_coins
        ]);

        if ($result) {
            return response()->json(['data' => 'Request Successful!', 'new_coins' => $new_coins], 200);
        } else {
            return response()->json(['error' => 'Error Occured, Request Failed!'], 500);
        }
    }
}
