<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\UserVideoController;
use App\Http\Controllers\Api\V1\TemplateController;


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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::group([
    'middleware' => ['api', \App\Http\Middleware\TelescopeMiddleware::class],
    'prefix' => 'v1/auth'
], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('me', [AuthController::class, 'me'])->name('me');
    Route::post('register',[AuthController::class, 'register'])->name('register');

    // Rutas para los favoritos (protegidas con middleware auth:api)
    Route::get('favorites', [FavoriteController::class, 'index']); 
    Route::post('favorites', [FavoriteController::class, 'store']);
    Route::delete('favorites/{id}', [FavoriteController::class, 'destroy']);

    // Rutas para Template
    Route::get('templates', [TemplateController::class, 'index']);
    Route::get('templates/{id}', [TemplateController::class, 'show']);
    Route::post('templates', [TemplateController::class, 'store']);
    Route::post('templates/{id}/generate', [TemplateController::class, 'generate']);

    // Rutas para UserVideo
    Route::apiResource('userVideos', UserVideoController::class)->only(['index', 'show', 'destroy']);
    Route::get('userVideos/{id}/preview', [UserVideoController::class, 'preview']);
    Route::get('userVideos/{id}/download', [UserVideoController::class, 'downloadVideo']);
});

// En una sola línea
Route::apiResource('v1/posts', PostController::class)
    ->middleware(['api', \App\Http\Middleware\TelescopeMiddleware::class]);