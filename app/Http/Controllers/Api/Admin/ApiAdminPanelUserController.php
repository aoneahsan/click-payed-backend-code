<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\User;
use App\Model\UserDetails;
use App\Model\UserAccount;
use App\Model\UserAchievements;
use App\Model\UserTransactionHistory;
use App\Model\TopupWalletRequest;
use App\Model\WithDrawalRequest;

// Resources
// Simple User Related Resources
use App\Http\Resources\UserResource;
use App\Http\Resources\UserAccountResource;
use App\Http\Resources\UserDetailsResource;
use App\Http\Resources\SearchUserResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserTransactionHistoryResource;

// AdminPanel Component Resources
// use App\Http\Resources;


class ApiAdminPanelUserController extends Controller
{
    public function getDepositRequests(Request $request)
    {
        $all_requests = TopupWalletRequest::all();
    }
}
