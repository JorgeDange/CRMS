<?php

use App\Models\Opportunity;
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

// 'pipeline/nova' DEVE vir antes do binding {opportunity}.
Route::view('pipeline/nova', 'opportunities.create')
    ->middleware(['auth', 'verified'])
    ->name('opportunities.create');

Route::get('pipeline/{opportunity}', function (Opportunity $opportunity) {
    return view('opportunities.show', ['opportunity' => $opportunity]);
})->middleware(['auth', 'verified'])->name('opportunities.show');

Route::view('ui', 'ui-demo')
    ->middleware(['auth', 'verified'])
    ->name('ui.demo');

require __DIR__.'/auth.php';
