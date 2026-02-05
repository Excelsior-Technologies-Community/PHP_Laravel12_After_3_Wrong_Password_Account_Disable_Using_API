<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:accounts',
            'password' => 'required|min:6'
        ]);

        $account = Account::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account registered successfully',
            'data' => $account
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $account = Account::where('email', $request->email)->first();

        if (!$account) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found'
            ], 404);
        }

        //  Check disabled account
        if (!$account->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Account disabled due to multiple wrong passwords'
            ], 403);
        }

        //  Wrong password
        if (!Hash::check($request->password, $account->password)) {

            $account->increment('wrong_attempts');

            if ($account->wrong_attempts >= 3) {
                $account->update(['is_active' => false]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid password',
                'wrong_attempts' => $account->wrong_attempts
            ], 401);
        }

        //  Correct password → reset attempts
        $account->update(['wrong_attempts' => 0]);

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => $account
        ]);
    }
}
