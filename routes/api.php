<?php

use Illuminate\Support\Facades\Route;

Use App\Http\Controllers\AcademicYearController;
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

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('me', [AuthController::class, 'me'])->name('me');

    Route::get('branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('branch/{id}', [BranchController::class, 'read'])->name('branch.read');
    Route::post('branch', [BranchController::class, 'create'])->name('branch.create');
    Route::put('branch/{id}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('branch/{id}', [BranchController::class, 'delete'])->name('branch.delete');

    Route::get('role', [RoleController::class, 'index'])->name('role.index');
    Route::get('role/{id}', [RoleController::class, 'read'])->name('role.read');
    Route::post('role', [RoleController::class, 'create'])->name('role.create');
    Route::put('role/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('role/{id}', [RoleController::class, 'delete'])->name('role.delete');

    Route::get('academic_year', [AcademicYearController::class, 'index'])->name('academic_year.index');
    Route::get('academic_year/{id}', [AcademicYearController::class, 'read'])->name('academic_year.read');
    Route::get('academic_year/byBranch/{branch_id}', [AcademicYearController::class, 'readByBranch'])->name('academic_year.readByBranch');
    Route::post('academic_year', [AcademicYearController::class, 'create'])->name('academic_year.create');
    Route::put('academic_year/{id}', [AcademicYearController::class, 'update'])->name('academic_year.update');
    Route::delete('academic_year/{id}', [AcademicYearController::class, 'delete'])->name('academic_year.delete');
});
