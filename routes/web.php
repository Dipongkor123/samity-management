<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SamityController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('login'));

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login',           [LoginController::class,          'showLoginForm'])->name('login');
    Route::post('/login',          [LoginController::class,          'login'])->name('login.post');
    Route::get('/register',        [RegisterController::class,       'showRegistrationForm'])->name('register');
    Route::post('/register',       [RegisterController::class,       'register'])->name('register.post');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password',[ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class,  'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',       [DashboardController::class,      'index'])->name('dashboard');
    Route::post('/logout',         [LoginController::class,          'logout'])->name('logout');
    Route::get('/change-password', [ChangePasswordController::class, 'showForm'])->name('change-password');
    Route::post('/change-password',[ChangePasswordController::class, 'update'])->name('change-password.update');

    // CRUD Resources
    Route::resource('samities',   SamityController::class)->except(['show']);
    Route::resource('members',    MemberController::class)->except(['show']);
    Route::resource('deposits',   DepositController::class)->except(['show']);
    Route::resource('loans',      LoanController::class)->except(['show']);
    Route::resource('repayments', RepaymentController::class)->except(['show']);
    Route::resource('fines',      FineController::class)->except(['show']);

    // Reports (read-only)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
