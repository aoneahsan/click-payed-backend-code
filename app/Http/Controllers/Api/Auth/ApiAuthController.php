<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Model\UserAccount;
use App\Model\UserDetails;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class ApiAuthController extends Controller
{

    public function loginApi(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone_number' => ['The provided credentials are incorrect.']
            ]);
        }

        return response()->json(['data' => new UserResource($user)], 201);
    }

    public function registerApi(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'unique:users']
        ]);

        $new_user_role = 'user';
        if ($request->role) {
            $new_user_role = $request->role;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role' => $new_user_role
        ]);
        UserDetails::create([
            'user_id' => $user->id
        ]);
        UserAccount::create([
            'user_id' => $user->id
        ]);
        $user->assignRole('user');

        return response()->json(['data' => new UserResource($user)], 201);
    }

    public function logoutApi(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['data' => 'User Tokkens Deleted'], 200);
    }

}
