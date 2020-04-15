<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{

    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $tokken = $user->createToken($request->device_name)->plainTextToken;

        $response = [
            'user' => $user,
            'tokken' => $tokken,
        ];

        return $response()->json(['data' => $response], 201);
    }

    public function registerApi(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokken = $user->createToken($request->device_name)->plainTextToken;

        $result = [
            'user' => $user,
            'tokken' => $tokken,
        ];

        return response()->json(['data' => $result], 201);
    }

}
