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
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SavingsPlanController;
use App\Http\Controllers\SavingsDepositController;
use App\Http\Controllers\SavingsWithdrawalController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CollectionController;

Route::get('/', fn() => redirect()->route('login'));

// Language switcher (works for guests and auth users)
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

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
    Route::resource('samities', SamityController::class);
    Route::post('samities/{samity}/members',              [SamityController::class, 'assignMember'])->name('samities.members.assign');
    Route::delete('samities/{samity}/members/{user}',     [SamityController::class, 'removeMember'])->name('samities.members.remove');
    Route::patch('samities/{samity}/members/{user}/toggle',[SamityController::class, 'toggleMember'])->name('samities.members.toggle');
    Route::resource('members',    MemberController::class);
    Route::resource('deposits',   DepositController::class)->except(['show']);
    Route::resource('loans',      LoanController::class)->except(['show']);
    Route::get('loans/{loan}/schedule', [LoanController::class, 'schedule'])->name('loans.schedule');
    Route::resource('repayments', RepaymentController::class)->except(['show']);
    Route::resource('fines',      FineController::class)->except(['show']);

    // Savings Module
    Route::prefix('savings')->name('savings.')->group(function () {
        Route::resource('plans',       SavingsPlanController::class)->except(['show', 'create', 'edit'])->parameters(['plans' => 'plan']);
        Route::resource('deposits',    SavingsDepositController::class)->except(['show', 'create', 'edit'])->parameters(['deposits' => 'deposit']);
        Route::resource('withdrawals', SavingsWithdrawalController::class)->except(['show', 'create', 'edit'])->parameters(['withdrawals' => 'withdrawal']);
    });

    // Reports
    Route::get('/reports',              [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/members',      [ReportController::class, 'members'])->name('reports.members');
    Route::get('/reports/loans',        [ReportController::class, 'loans'])->name('reports.loans');
    Route::get('/reports/collections',  [ReportController::class, 'collections'])->name('reports.collections');
    Route::get('/reports/defaulters',   [ReportController::class, 'defaulters'])->name('reports.defaulters');

    // Accounting / Cash Book
    Route::resource('accounts', AccountController::class)->except(['show', 'create', 'edit']);

    // Staff Management
    Route::resource('staff', StaffController::class)->except(['show', 'create', 'edit']);

    // Collection Module
    Route::get('/collection/bulk',         [CollectionController::class, 'bulk'])->name('collection.bulk');
    Route::post('/collection/bulk',        [CollectionController::class, 'storeBulk'])->name('collection.store-bulk');
    Route::get('/collection/daily',        [CollectionController::class, 'daily'])->name('collection.daily');
});
