<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function roleGivePermission(Request $request)
    {
        $role = Role::query()->findOrFail($request->role_id);
        $permission = Permission::query()->whereKey($request->permission_ids)->get();
        $role->syncPermissions($permission);

        return response()->json(['message' => 'Update thành công']);
    }
}
