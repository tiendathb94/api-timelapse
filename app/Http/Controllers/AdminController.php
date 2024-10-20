<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->username;

        $user = Admin::query()
            ->where('email', $username)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Your account is not found '], 400);
        }

        if ($user->status == 0) {
            return response()->json(['message' => 'Your account was blocked'], 400);
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json(['message' => 'Your account is not correct'], 400);
        }

        $user->tokens()->delete();

        $access_token = $user->createToken($user->name)->plainTextToken;

        $minutes = config('sanctum.expiration');

        return response()->json([
            'data' => [
                'token' => $access_token,
                'type' => 'Bearer',
                'expried_time' => Carbon::now()->addMinutes($minutes)->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function getUser()
    {
        $user = Auth::user();
        return response()->json(['data' => $user]);
    }

}
