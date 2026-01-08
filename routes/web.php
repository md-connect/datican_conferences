<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

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
