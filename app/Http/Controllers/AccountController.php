<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $group = $user->group_owner;
        $users = [];
        if ($group) {
            $users = User::where('id', '!=', $user->id)->where('group_id', $group->id)->orderBy('created_at', "DESC")->get();
        }
        return response()->json([
            'data' => $users
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $requestData = $request->all();
        $group = $user->group_owner;
        try {
            if ($group) {
                $data = [
                    'name' => $requestData['name'],
                    'email' => $requestData['email'],
                    'password' => Hash::make($requestData['password']),
                    'group_id' => $group->id,
                    'status' => 1,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ];
                $newUser = User::create($data);
                return response()->json([
                    'data' => $newUser,
                    'message' => 'Create successful'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Create Error'
            ], 400);
        }

        return response()->json([
            'message' => 'Create Error'
        ], 400);
    }

    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
        $requestData = $request->get('user_id');
        $group = $user->group_owner;
        try {
            if ($group) {
                $data = [
                    'name' => $requestData['name'],
                    'email' => $requestData['email'],
                    'password' => Hash::make($requestData['password']),
                    'group_id' => $group->id,
                    'status' => 1,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ];
                $newUser = User::create($data);
                return response()->json([
                    'data' => $newUser,
                    'message' => 'Create successful'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Create Error'
            ], 400);
        }

        return response()->json([
            'message' => 'Create Error'
        ], 400);
    }
}
