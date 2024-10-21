<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\TimelapseController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:user-api'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    Route::get('user', [AuthController::class, 'getUser']);
    Route::get('accounts', [AccountController::class, 'index']);
    Route::post('accounts/create', [AccountController::class, 'create']);
    Route::get('get-image', [ImageController::class, 'getImage']);

    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('cameras', [CameraController::class, 'index']);

    Route::get('/timelapse', [TimelapseController::class, 'index']);
    Route::post('/create-timelapse', [TimelapseController::class, 'createTimelapse']);
    
    Route::post('role-give-permission', [RolePermissionController::class, 'roleGivePermission']);
    Route::post('assign-role', [RolePermissionController::class, 'assignRole']);

});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::post('login', [AdminController::class, 'login'])->name('login');
    Route::group(['middleware' => 'auth:admin-api'], function(){
        Route::get('user', [AdminController::class, 'getUser']);
        Route::resource('groups', GroupController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        Route::post('role-give-permission', [RolePermissionController::class, 'roleGivePermission']);
        Route::post('assign-role', [RolePermissionController::class, 'assignRole']);

        Route::get('list-project', [AdminController::class, 'listProject']);
        Route::get('list-user', [AdminController::class, 'listUser']);
        Route::get('list-camera', [AdminController::class, 'listCamera']);
    });
});
