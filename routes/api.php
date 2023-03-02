<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AcademicSessionController;
Use App\Http\Controllers\AcademicYearController;
Use App\Http\Controllers\NoticeTypeController;
Use App\Http\Controllers\SchoolInfoController;
Use App\Http\Controllers\ClassroomController;
Use App\Http\Controllers\ReligionController;
Use App\Http\Controllers\BranchController;
Use App\Http\Controllers\NoticeController;
Use App\Http\Controllers\DriverController;
Use App\Http\Controllers\RoleController;
Use App\Http\Controllers\AuthController;
Use App\Http\Controllers\UserController;

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

    Route::get('classroom', [ClassroomController::class, 'index'])->name('classroom.index');

});

Route::post('school_info/{id}', [SchoolInfoController::class, 'update'])->name('school_info.update');

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
Route::get('academic_year/by_branch/{branch_id}', [AcademicYearController::class, 'readByBranch'])->name('academic_year.readByBranch');
Route::post('academic_year', [AcademicYearController::class, 'create'])->name('academic_year.create');
Route::put('academic_year/{id}', [AcademicYearController::class, 'update'])->name('academic_year.update');
Route::delete('academic_year/{id}', [AcademicYearController::class, 'delete'])->name('academic_year.delete');

Route::get('academic_session/{id}', [AcademicSessionController::class, 'read'])->name('academic_session.read');
Route::get('academic_session/by_year/{academic_year_id}', [AcademicSessionController::class, 'readByYear'])->name('academic_session.readByYear');
Route::put('academic_session/{id}', [AcademicSessionController::class, 'update'])->name('academic_session.update');
Route::delete('academic_session/{id}', [AcademicSessionController::class, 'delete'])->name('academic_session.delete');

Route::get('religion', [ReligionController::class, 'index'])->name('religion.index');

Route::get('notice_type', [NoticeTypeController::class, 'index'])->name('notice_type.index');
Route::get('notice_type/{id}', [NoticeTypeController::class, 'read'])->name('notice_type.read');
Route::post('notice_type', [NoticeTypeController::class, 'create'])->name('notice_type.create');
Route::put('notice_type/{id}', [NoticeTypeController::class, 'update'])->name('notice_type.update');
Route::delete('notice_type/{id}', [NoticeTypeController::class, 'delete'])->name('notice_type.delete');

Route::get('notice', [NoticeController::class, 'index'])->name('notice.index');
Route::get('notice/{id}', [NoticeController::class, 'read'])->name('notice.read');
Route::post('notice', [NoticeController::class, 'create'])->name('notice.create');
Route::put('notice/{id}', [NoticeController::class, 'update'])->name('notice.update');
Route::delete('notice/{id}', [NoticeController::class, 'delete'])->name('notice.delete');

Route::get('classroom/{id}', [ClassroomController::class, 'read'])->name('classroom.read');
Route::get('classroom/by_branch/{branch_id}', [ClassroomController::class, 'readByBranch'])->name('classroom.readByBranch');
Route::get('classroom/by_status/{status_id}', [ClassroomController::class, 'readByStatus'])->name('classroom.readByStatus');
Route::get('classroom/by_branch/by_status/{branch_id}/{status_id}', [ClassroomController::class, 'readByBranchAndStatus'])->name('classroom.readByBranchAndStatus');
Route::post('classroom', [ClassroomController::class, 'create'])->name('classroom.create');
Route::put('classroom/{id}', [ClassroomController::class, 'update'])->name('classroom.update');
Route::delete('classroom/{id}', [ClassroomController::class, 'delete'])->name('classroom.delete');

Route::get('driver', [DriverController::class, 'index'])->name('driver.index');
Route::post('driver', [DriverController::class, 'create'])->name('driver.create');
Route::post('driver/{id}', [DriverController::class, 'update'])->name('driver.update');
Route::delete('driver/{id}', [DriverController::class, 'delete'])->name('driver.delete');
