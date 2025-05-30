<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAttendanceEditController;
use App\Http\Controllers\AdminIndexController;
use App\Http\Controllers\AdminListController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AttendanceEditController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StaffAttendanceListController;
use App\Http\Controllers\StaffLoginController;
use App\Http\Controllers\StaffRequestController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('/email/verification-notification', [VerificationController::class, 'resend'])->name('verification.send');

Route::get('/login',[StaffLoginController::class, 'show'])->name('staff.login');
Route::post('/login', [StaffLoginController::class, 'login']);

Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.show');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

Route::middleware(['staff'])->group(function () {
    Route::post('/logout', [StaffLoginController::class, 'logout']);
    Route::get('/attendance',[IndexController::class, 'show'])->name('home.show');
    Route::post('/attendance/clock-in', [IndexController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [IndexController::class, 'clockOut'])->name('attendance.clock-out');
    Route::post('/attendance/break-start', [IndexController::class, 'startBreak'])->name('attendance.break-start');
    Route::post('/attendance/break-end', [IndexController::class, 'endBreak'])->name('attendance.break-end');
    Route::get('/attendance/list', [AttendanceListController::class,'list'])->name('staff.list');
    Route::post('/attendance/change-month', [AttendanceListController::class, 'changeMonth'])->name('staff.changeMonth');

});

Route::middleware(['admin'])->group(function () {
    Route::post('/admin/logout', [AdminLoginController::class, 'logout']);
    Route::get('/admin/attendance/list', [AdminIndexController::class,'list'])->name('admin.list');
    Route::post('/admin/attendance/change-date', [AdminIndexController::class, 'changeDate'])->name('admin.changeDate');
    Route::get('/admin/staff/list',[AdminListController::class,'show'])->name('admin.staff.list');
    Route::get('/stamp_correction_request/approve/{id}',[ApprovalController::class,'show'])->name('admin.application');
    Route::post('/stamp_correction_request/approve/{id}',[ApprovalController::class,'approval'])->name('admin.approval');
    Route::post('/admin/attendance/staff/change-month', [StaffAttendanceListController::class, 'changeMonth'])->name('admin.changeMonth');
    Route::get('/admin/attendance/staff/{id}', [StaffAttendanceListController::class, 'list'])->name('admin.attendanceList');
    Route::get('/admin/attendance/export/{id}', [StaffAttendanceListController::class, 'download'])->name('admin.attendance.export');
});

Route::get('/attendance/{id}', fn () => 'redirecting...')
    ->middleware('route.by.role')
    ->name('attendance.redirect.get');
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/attendance/{id}', [AdminAttendanceEditController::class, 'detail'])->name('admin.detail');
    Route::post('/attendance/{id}', [AdminAttendanceEditController::class, 'update'])->name('admin.update');
});
Route::prefix('staff')->middleware('auth:users')->group(function () {
    Route::get('/attendance/{id}', [AttendanceEditController::class, 'detail'])->name('staff.detail');
    Route::post('/attendance/{id}', [AttendanceEditController::class, 'store'])->name('staff.application');
});

Route::get('/stamp_correction_request/list', function (){
})->middleware('route.by.role');
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/stamp_correction_request/list',[AdminRequestController::class,'show'])->name('admin.request');
});
Route::prefix('staff')->middleware('auth:users')->group(function () {
    Route::get('/stamp_correction_request/list',[StaffRequestController::class,'show'])->name('staff.request');
});
