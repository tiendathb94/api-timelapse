<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = Project::query()->where([
            'group_id' => $user->group_id,
            'active' => 1
        ])->get();
        return response()->json(['data' => $projects]);
    }
}
