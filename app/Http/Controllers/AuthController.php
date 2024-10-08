<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $username = $request->username;

        $user = User::query()
            ->where('email', $username)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Tài khoản không tồn tại trên hệ thống'], 400);
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json(['message' => 'Thông tin tài khoản không chính xác'], 400);
        }

        $user->tokens()->delete();

        $access_token = $user->createToken($user->id)->plainTextToken;

        $minutes = config('sanctum.expiration');

        return response()->json([
            'data' => [
                'token' => $access_token,
                'type' => 'Bearer',
                'expried_time' => Carbon::now()->addMinutes($minutes)->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function getUser()
    {
        return response()->json(['data' => Auth::user()]);
    }

    public function changePassword(Request $request)
    {
        $password = $request->get('password');

        $user = User::query()->find(Auth::id());

        $user->password = Hash::make($password);
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }
}
