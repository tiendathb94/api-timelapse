<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CameraController extends Controller
{
    public function index(Request $request)
    {
        $project_id = $request->project_id;
        $user = Auth::user();

        $exists = Project::query()->where([
            'group_id' => $user->group->id,
            'active'   => 1
        ])->exists();
        if (!$exists) return response()->json(['message' => 'Project does not exists'], 400);

        $cameras = Camera::query()->where([
            'project_id' => $project_id,
            'active' => 1
        ])->get();
        return response()->json(['data' => $cameras]);
    }
}
