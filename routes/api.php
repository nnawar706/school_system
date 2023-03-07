<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LibraryBookCategoryController;
use App\Http\Controllers\AcademicSessionController;
Use App\Http\Controllers\TransportationController;
Use App\Http\Controllers\TransportRouteController;
Use App\Http\Controllers\LibraryShelfController;
Use App\Http\Controllers\AcademicYearController;
Use App\Http\Controllers\DesignationController;
Use App\Http\Controllers\SchoolInfoController;
Use App\Http\Controllers\NoticeTypeController;
Use App\Http\Controllers\ClassroomController;
Use App\Http\Controllers\ReligionController;
Use App\Http\Controllers\WeekdayController;
Use App\Http\Controllers\SubjectController;
Use App\Http\Controllers\TeacherController;
Use App\Http\Controllers\BranchController;
Use App\Http\Controllers\NoticeController;
Use App\Http\Controllers\DriverController;
Use App\Http\Controllers\GenderController;
Use App\Http\Controllers\MonthController;
Use App\Http\Controllers\BatchController;
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

});

Route::post('school_info/{id}', [SchoolInfoController::class, 'update'])->name('school_info.update');
Route::get('school_info/{id}', [SchoolInfoController::class, 'read'])->name('school_info.read');

Route::get('branch', [BranchController::class, 'index'])->name('branch.index');
Route::get('branch/{id}', [BranchController::class, 'read'])->name('branch.read');
Route::post('branch', [BranchController::class, 'create'])->name('branch.create');
Route::put('branch/{id}', [BranchController::class, 'update'])->name('branch.update');
Route::delete('branch/{id}', [BranchController::class, 'delete'])->name('branch.delete');

Route::get('role', [RoleController::class, 'index'])->name('role.index');
//Route::get('role/{id}', [RoleController::class, 'read'])->name('role.read');
//Route::post('role', [RoleController::class, 'create'])->name('role.create');
//Route::put('role/{id}', [RoleController::class, 'update'])->name('role.update');
//Route::delete('role/{id}', [RoleController::class, 'delete'])->name('role.delete');

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
Route::get('weekday', [WeekdayController::class, 'index'])->name('weekday.index');
Route::get('gender', [GenderController::class, 'index'])->name('gender.index');
Route::get('month', [MonthController::class, 'index'])->name('month.index');

Route::get('teacher', [TeacherController::class, 'index'])->name('teacher.index');
Route::get('teacher/{id}', [TeacherController::class, 'read'])->name('teacher.read');
Route::get('teacher/by_expertise/{subject_id}', [TeacherController::class, 'readByExpertise'])->name('teacher.readByExpertise');
Route::get('teacher/by_branch/{branch_id}', [TeacherController::class, 'readByBranch'])->name('teacher.readByBranch');
Route::get('teacher/by_designation/{designation_id}', [TeacherController::class, 'readByDesignation'])->name('teacher.readByDesignation');
Route::post('teacher', [TeacherController::class, 'create'])->name('teacher.create');
Route::post('teacher/{id}', [TeacherController::class, 'update'])->name('teacher.update');

Route::get('notice_type', [NoticeTypeController::class, 'index'])->name('notice_type.index');
Route::get('notice_type/{id}', [NoticeTypeController::class, 'read'])->name('notice_type.read');
Route::post('notice_type', [NoticeTypeController::class, 'create'])->name('notice_type.create');
Route::put('notice_type/{id}', [NoticeTypeController::class, 'update'])->name('notice_type.update');
Route::delete('notice_type/{id}', [NoticeTypeController::class, 'delete'])->name('notice_type.delete');

Route::get('notice', [NoticeController::class, 'index'])->name('notice.index');
Route::get('notice/{id}', [NoticeController::class, 'read'])->name('notice.read');
Route::get('notice/by_notice_type/{notice_type_id}', [NoticeController::class, 'readByType'])->name('notice.readByType');
Route::get('notice/by_branch/{branch_id}', [NoticeController::class, 'readByBranch'])->name('notice.readByBranch');
Route::get('notice/by_branch/by_type/{branch_id}/{type_id}', [NoticeController::class, 'readByBranchAndType'])->name('notice.readByBranchAndType');
Route::post('notice', [NoticeController::class, 'create'])->name('notice.create');
Route::put('notice/{id}', [NoticeController::class, 'update'])->name('notice.update');
Route::delete('notice/{id}', [NoticeController::class, 'delete'])->name('notice.delete');

Route::get('class', [BatchController::class, 'index'])->name('batch.index');
Route::get('class/{id}', [BatchController::class, 'read'])->name('batch.read');
Route::get('class/by_branch/{branch_id}', [BatchController::class, 'readByBranch'])->name('batch.readByBranch');
Route::post('class', [BatchController::class, 'create'])->name('batch.create');
Route::put('class/{id}', [BatchController::class, 'update'])->name('batch.update');
Route::delete('class/{id}', [BatchController::class, 'delete'])->name('batch.delete');

Route::get('subject', [SubjectController::class, 'index'])->name('subject.index');
Route::post('subject', [SubjectController::class, 'create'])->name('subject.create');
Route::put('subject/{id}', [SubjectController::class, 'update'])->name('subject.update');
Route::delete('subject/{id}', [SubjectController::class, 'delete'])->name('subject.delete');

Route::get('classroom', [ClassroomController::class, 'index'])->name('classroom.index');
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

Route::get('designation', [DesignationController::class, 'index'])->name('designation.index');
Route::post('designation', [DesignationController::class, 'create'])->name('designation.create');
Route::put('designation/{id}', [DesignationController::class, 'update'])->name('designation.update');
Route::delete('designation/{id}', [DesignationController::class, 'delete'])->name('designation.delete');

Route::get('transport_route', [TransportRouteController::class, 'index'])->name('transport_route.index');
Route::post('transport_route', [TransportRouteController::class, 'create'])->name('transport_route.create');
Route::put('transport_route/{id}', [TransportRouteController::class, 'update'])->name('transport_route.update');
Route::delete('transport_route/{id}', [TransportRouteController::class, 'delete'])->name('transport_route.delete');

Route::get('transport', [TransportationController::class, 'index'])->name('transport.index');
Route::post('transport', [TransportationController::class, 'create'])->name('transport.create');
Route::put('transport/{id}', [TransportationController::class, 'update'])->name('transport.update');
Route::delete('transport/{id}', [TransportationController::class, 'delete'])->name('transport.delete');

Route::get('library_shelf', [LibraryShelfController::class, 'index'])->name('library_shelf.index');
Route::post('library_shelf', [LibraryShelfController::class, 'create'])->name('library_shelf.create');
Route::put('library_shelf/{id}', [LibraryShelfController::class, 'update'])->name('library_shelf.update');
Route::delete('library_shelf/{id}', [LibraryShelfController::class, 'delete'])->name('library_shelf.delete');

Route::get('library_book_category', [LibraryBookCategoryController::class, 'index'])->name('library_book_category.index');
Route::post('library_book_category', [LibraryBookCategoryController::class, 'create'])->name('library_book_category.create');
Route::put('library_book_category/{id}', [LibraryBookCategoryController::class, 'update'])->name('library_book_category.update');
Route::delete('library_book_category/{id}', [LibraryBookCategoryController::class, 'delete'])->name('library_book_category.delete');
