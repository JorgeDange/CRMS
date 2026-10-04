<?php

use App\Exports\OpportunityTemplateExport;
use App\Models\Opportunity;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

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

// 'pipeline/importar' DEVE vir antes do binding {opportunity}.
Route::view('pipeline/importar', 'opportunities.import')
    ->middleware(['auth', 'verified'])
    ->name('opportunities.import');

Route::get('pipeline/importar/template', function () {
    return Excel::download(new OpportunityTemplateExport, 'template-oportunidades.xlsx');
})->middleware(['auth', 'verified'])->name('opportunities.import.template');

Route::get('pipeline/{opportunity}', function (Opportunity $opportunity) {
    return view('opportunities.show', ['opportunity' => $opportunity]);
})->middleware(['auth', 'verified'])->name('opportunities.show');

Route::view('ui', 'ui-demo')
    ->middleware(['auth', 'verified'])
    ->name('ui.demo');

require __DIR__.'/auth.php';
