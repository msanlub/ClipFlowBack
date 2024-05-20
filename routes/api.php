<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
    ], function ($router) {
    Route::post('login', [\App\Http\Controllers\Api\V1\AuthController::class,
    'login'])->name('login');
    Route::post('logout', [\App\Http\Controllers\Api\V1\AuthController::class,
    'logout'])->name('logout');
    Route::post('refresh', [\App\Http\Controllers\Api\V1\AuthController::class,
    'refresh'])->name('refresh');
    Route::post('me', [\App\Http\Controllers\Api\V1\AuthController::class,
    'me'])->name('me');
    });

Route::apiResource('v1/posts', 
         App\Http\Controllers\Api\V1\PostController::class)->middleware('api');
