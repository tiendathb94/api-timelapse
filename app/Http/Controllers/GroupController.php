<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $data = Group::query()->paginate();
        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $data = Group::query()->findOrFail($id);
        $data->load('projects.cameras');
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->only(['name', 'code', 'user_id', 'active']);

        Group::query()->whereKey($id)->update($data);

        return response()->json(['message' => 'Update thanh cong']);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'code', 'user_id', 'active']);

        return response()->json(['data' => Group::query()->create($data)]);
    }
}
