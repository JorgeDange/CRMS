<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('pipeline', 'opportunities.pipeline')
    ->middleware(['auth', 'verified'])
    ->name('pipeline');

Route::view('ui', 'ui-demo')
    ->middleware(['auth', 'verified'])
    ->name('ui.demo');

require __DIR__.'/auth.php';
