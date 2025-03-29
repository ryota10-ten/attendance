<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminIndexController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StaffLoginController;
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

Route::get('/attendance',[IndexController::class, 'show'])->name('home.show');
Route::post('/attendance/clock-in', [IndexController::class, 'clockIn'])->name('attendance.clock-in');
Route::post('/attendance/clock-out', [IndexController::class, 'clockOut'])->name('attendance.clock-out');
Route::post('/attendance/break-start', [IndexController::class, 'startBreak'])->name('attendance.break-start');
Route::post('/attendance/break-end', [IndexController::class, 'endBreak'])->name('attendance.break-end');

Route::get('/login',[StaffLoginController::class, 'show'])->name('staff.login');
Route::post('/login', [StaffLoginController::class, 'login']);
Route::post('/logout', [StaffLoginController::class, 'logout']);

Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.show');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout']);

Route::get('/admin/attendance/list', [AdminIndexController::class,'list'])->name('admin.list');
Route::post('/attendance/change-date', [AdminIndexController::class, 'changeDate'])->name('admin.changeDate');

Route::get('/attendance/list', [AttendanceListController::class,'list'])->name('staff.list');
Route::post('/attendance/change-date', [AttendanceListController::class, 'changeDate'])->name('staff.changeDate');
