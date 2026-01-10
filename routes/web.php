<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RegistrationExportController;
use App\Http\Controllers\ConferenceRegistrationController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/call-for-papers', [PageController::class, 'callForPapers'])->name('call-for-papers');
Route::get('/committees', [PageController::class, 'committees'])->name('committees');
Route::get('/register', [PageController::class, 'register'])->name('register');
Route::get('/acknowledgement', [PageController::class, 'acknowledgement'])->name('acknowledgement');

// Conference Registration Routes (separate from PageController register)
Route::prefix('conference')->group(function () {
    Route::get('2026-registration', [\App\Http\Controllers\ConferenceRegistrationController::class, 'showRegistrationForm'])
        ->name('conference.registration');
    
    Route::post('2026-registration', [\App\Http\Controllers\ConferenceRegistrationController::class, 'register'])
        ->name('conference.register');
    
    Route::get('success', [\App\Http\Controllers\ConferenceRegistrationController::class, 'success'])
        ->name('conference.registration.success');

    Route::get('stats', [\App\Http\Controllers\ConferenceRegistrationController::class, 'stats'])
        ->name('conference.registration.stats')
        ->middleware('auth');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    // Authentication (public)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // Protected Admin Routes using middleware
    Route::middleware(['web', 'admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('registrations', [DashboardController::class, 'registrations'])->name('admin.registrations');
        Route::get('registrations/{id}', [DashboardController::class, 'showRegistration'])->name('admin.registration.show');
        
        // Stats route (moved inside admin prefix)
        Route::get('stats', [ConferenceRegistrationController::class, 'stats'])
            ->name('conference.registration.stats');
        
        // Export Routes
        Route::get('export/registrations', [RegistrationExportController::class, 'export'])
            ->name('admin.export.registrations');
    });
});

