<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function roleGivePermission(Request $request)
    {
        $role = Role::query()->where('guard_name', getGuardName())->findOrFail($request->role_id);
        $permission = Permission::query()->where('guard_name', getGuardName())->whereKey($request->permission_ids)->get();
        $role->syncPermissions($permission);

        return response()->json(['message' => 'Update thành công']);
    }

    public function assignRole(Request $request)
    {
        $user = User::query()->findOrFail($request->user_id);
        $roles = Role::query()->whereKey($request->roles)->get();
        $user->syncRoles($roles);
        return response()->json(['message' => 'Update thành công']);
    }
}
