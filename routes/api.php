<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login_api', 'Api\Auth\ApiAuthController@loginApi');
Route::post('register_api', 'Api\Auth\ApiAuthController@registerApi');

// ********************************************************************************
// ***************************   Admin Panel Routes    ****************************
// ********************************************************************************

Route::group([
    'middleware' => 'auth:sanctum',
    'namespace' => 'Api'
], function () {

    // Deposit Component Routes APIs
    Route::post('get_all_deposit_accounts', 'Admin\ApiAdminPanelUserController@getAllDepositAccounts');
    Route::post('add_new_deposit_account', 'Admin\ApiAdminPanelUserController@addNewDepositAccount');
    Route::post('make_deposit_request', 'Admin\ApiAdminPanelUserController@makeDepositRequest');
    Route::post('get_deposit_requests', 'Admin\ApiAdminPanelUserController@getDepositRequests');
    Route::post('approve_deposit_request', 'Admin\ApiAdminPanelUserController@approveDepositRequest');
    Route::post('reject_deposit_request', 'Admin\ApiAdminPanelUserController@rejectDepositRequest');

    // Withdrawal Component Routes APIs
    Route::post('get_all_withdrawal_requests', 'Admin\ApiAdminPanelUserController@getAllWithdrawalRequests');
    Route::post('approve_withdrawal_request', 'Admin\ApiAdminPanelUserController@approveWithdrawalRequest');
    Route::post('reject_withdrawal_request', 'Admin\ApiAdminPanelUserController@rejectWithdrawalRequest');

    // Manage User Coins Component Routes
    Route::post('make_add_remove_user_coins_request', 'Admin\ApiAdminPanelUserController@makeAddRemoveUserCoinsRequest');

});


// ********************************************************************************
// ***************************  App User Routes Routes  ***************************
// ********************************************************************************

Route::group([
    'middleware' => 'auth:sanctum',
    'namespace' => 'Api'
], function () {

    // Logout Route
    Route::post('logout_api', 'Auth\ApiAuthController@logoutApi');

    // User Permissions Route Api
    Route::post('get_user_permissions', 'User\ApiUserController@getUserPermissions');
    Route::post('check_user_has_permissions', 'User\ApiUserController@checkUserHasPermission');

    // User Account Routes (get coins, balance etc)
    Route::get('get_user_account_data', 'User\ApiUserController@getUserAccountData');
    Route::get('get_user_details_data', 'User\ApiUserController@getUserDetailsData');

    // Search User Routes APIs
    Route::post('search_person_by_number', 'User\ApiUserController@searchPerson');

    // Buy Coins Api
    Route::post('buy_coins', 'User\ApiUserController@buyCoinsRequest');

    // Redeem Coins Api
    Route::post('redeem_coins', 'User\ApiUserController@redeemCoinsRequest');

    // Transfer Coins Routes Api
    Route::post('transfer_coins', 'User\ApiUserController@transferCoinsRequest');

    // Topup Wallet Requests Route API
    Route::post('topup_wallet', 'User\ApiUserController@submitTopupWalletRequest');

    // Withdrawal Request Route API
    Route::post('withdrawal_request', 'User\ApiUserController@submitWithdrawalRequest');

    // Update User Profile Route API
    Route::post('get_user_profile_data', 'User\ApiUserController@getUserProfileData');
    Route::post('update_user_profile_data', 'User\ApiUserController@updateUserProfile');

    // FireBase Plugin Route, Save Firebase Tokken when User Starts App
    Route::post('store_user_firebase_token', 'User\ApiUserController@saveUserFirebaseToken');
    Route::post('delete_user_firebase_token', 'User\ApiUserController@deleteUserFirebaseToken');

    // User Transaction Histroy Routes
    Route::post('fetch_user_transaction_history', 'User\ApiUserController@getTransactionHistory');
});