<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/call-for-papers', [PageController::class, 'callForPapers'])->name('call-for-papers');
Route::get('/committees', [PageController::class, 'committees'])->name('committees');
Route::get('/register', [PageController::class, 'register'])->name('register');