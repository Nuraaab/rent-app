<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

/*
|--------------------------------------------------------------------------
| Group API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for group management.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Group routes with authentication middleware
Route::middleware('auth:sanctum')->group(function () {
    
    // Group CRUD operations
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/groups/joined', [GroupController::class, 'joined']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups/{group}', [GroupController::class, 'show']);
    Route::put('/groups/{group}', [GroupController::class, 'update']);
    Route::delete('/groups/{group}', [GroupController::class, 'destroy']);
    
    // Group membership operations
    Route::post('/groups/{group}/join', [GroupController::class, 'join']);
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave']);
    
    // Search functionality
    Route::get('/groups/search', [GroupController::class, 'search']);
});

// Public group routes (no authentication required)
Route::get('/groups/public', [GroupController::class, 'index']);
