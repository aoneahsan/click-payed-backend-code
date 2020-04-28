<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:sanctum',
    'namespace' => 'Api'
], function () {

    // Logout Route
    Route::post('logout_api', 'Auth\ApiAuthController@logoutApi');

    // Test Routes Api
    Route::get('secure_test', function () {
        return response()->json(['data' => 'Secure Test ok working']);
    });

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
});