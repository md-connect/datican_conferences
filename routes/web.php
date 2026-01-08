<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/call-for-papers', [PageController::class, 'callForPapers'])->name('call-for-papers');
Route::get('/committees', [PageController::class, 'committees'])->name('committees');
Route::get('/register', [PageController::class, 'register'])->name('register');
Route::get('/acknowledgement', [PageController::class, 'acknowledgement'])->name('acknowledgement');

Route::get('register/2026-conference-registration', [ConferenceRegistrationController::class, 'showRegistrationForm'])
    ->name('conference.registration');

Route::post('register/2026-conference-registration', [ConferenceRegistrationController::class, 'register'])
    ->name('conference.register');

Route::get('register/2026-conference-registration/success', [ConferenceRegistrationController::class, 'success'])
    ->name('conference.registration.success');

Route::get('register/2026-conference-registration/stats', [ConferenceRegistrationController::class, 'stats'])
    ->name('conference.registration.stats')
    ->middleware('auth'); // Protect stats page with auth if needed