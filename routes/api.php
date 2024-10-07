<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('user', [AuthController::class, 'getUser']);
    Route::get('get-image', [ImageController::class, 'getImage']);

    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('cameras', [CameraController::class, 'index']);
});
