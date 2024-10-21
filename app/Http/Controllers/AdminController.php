<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Camera;
use App\Models\Project;
use App\Models\RequestVideoTimelapse;
use App\Models\User;
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

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        return response()->json(['message' => 'Logout succesful']);
    }

    public function getUser()
    {
        $user = Auth::user();
        $user->load(['roles']);
        return response()->json(['data' => $user]);
    }

    public function listTimelapses(Request $request)
    {
        $data = RequestVideoTimelapse::query()
            ->with(['user', 'camera'])
            ->paginate();

        return response()->json($data);
    }

    public function listProject(Request $request)
    {
        $group_id = $request->group_id;

        $data = Project::query()
            ->when($group_id, fn($q) => $q->where('group_id', $group_id))
            ->with('group')
            ->paginate();

        return response()->json($data);
    }

    public function listUser(Request $request)
    {
        $group_id = $request->group_id;

        $data = User::query()
            ->when($group_id, fn($q) => $q->where('group_id', $group_id))
            ->paginate();

        return response()->json($data);
    }

    public function listCamera(Request $request)
    {
        $group_id = $request->group_id;
        $project_id = $request->project_id;

        $data = Camera::query()
            ->when($group_id, fn($q) => $q->whereRaw("project_id IN (select id from projects where group_id = $group_id)"))
            ->when($project_id, fn($q) => $q->where('project_id', $project_id))
            ->paginate();

        return response()->json($data);
    }
}
