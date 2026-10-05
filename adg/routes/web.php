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

// Agenda Comercial (Sprint 5) — apps-tasks / apps-notes / apps-calendar.
Route::view('agenda', 'agenda.index')
    ->middleware(['auth', 'verified'])
    ->name('agenda');

// 'agenda/nova' DEVE vir antes de qualquer rota parametrizada.
Route::view('agenda/nova', 'agenda.create')
    ->middleware(['auth', 'verified'])
    ->name('agenda.create');

Route::view('agenda/calendario', 'agenda.calendar')
    ->middleware(['auth', 'verified'])
    ->name('agenda.calendar');

// Conciliações (Sprint 6) — payment.html.
Route::view('conciliações', 'conciliactions.index')
    ->middleware(['auth', 'verified'])
    ->name('conciliactions');

Route::view('conciliações/nova', 'conciliactions.create')
    ->middleware(['auth', 'verified'])
    ->name('conciliactions.create');

Route::view('conciliações/{conciliaction}', 'conciliactions.edit')
    ->middleware(['auth', 'verified'])
    ->name('conciliactions.edit');

Route::view('ui', 'ui-demo')
    ->middleware(['auth', 'verified'])
    ->name('ui.demo');

require __DIR__.'/auth.php';
