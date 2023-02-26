<?php

use Illuminate\Support\Facades\Route;

Use App\Http\Controllers\AuthController;
Use App\Http\Controllers\BranchController;
Use App\Http\Controllers\RoleController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:api'], function($routes) {

    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('branch', [BranchController::class, 'index']);
    Route::get('branch/{id}', [BranchController::class, 'read']);
    Route::post('branch', [BranchController::class, 'create']);
    Route::put('branch/{id}', [BranchController::class, 'update']);
    Route::delete('branch/{id}', [BranchController::class, 'delete']);

    Route::get('role', [RoleController::class, 'index']);
    Route::get('role/{id}', [RoleController::class, 'read']);
    Route::post('role', [RoleController::class, 'create']);
    Route::put('role/{id}', [RoleController::class, 'update']);
    Route::delete('role/{id}', [RoleController::class, 'delete']);
});
