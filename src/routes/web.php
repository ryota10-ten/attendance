<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminLoginController;
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

Route::get('/login',[StaffLoginController::class, 'show'])->name('staff.login');
Route::post('/login', [StaffLoginController::class, 'login']);

Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.show');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
