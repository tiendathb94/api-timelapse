<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProjectController;
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
});


Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::post('login', [AdminController::class, 'login'])->name('login');
    Route::group(['middleware' => 'auth:admin-api'], function(){
        Route::get('user', [AdminController::class, 'getUser']);
        Route::resource('groups', GroupController::class);
    });
});
