<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Agendamento de tarefas (Laravel 11+ usa routes/console.php).
 * Sprint 5: agenda:reminder diário às 20h.
 * Sprint 10: alerts:generate diário às 06h.
 */
Schedule::command('agenda:reminder')
    ->dailyAt('20:00')
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('alerts:generate')
    ->dailyAt('06:00')
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->runInBackground();

/**
 * Sprint 12: backup diário às 02h.
 */
Schedule::command('backup:run')
    ->dailyAt('02:00')
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->runInBackground();

/**
 * Sprint 12: cleanup de backups antigos diário às 03h.
 */
Schedule::command('backup:clean')
    ->dailyAt('03:00')
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->runInBackground();

/**
 * Sprint 12: monitor de saúde do backup diário às 04h.
 */
Schedule::command('backup:monitor')
    ->dailyAt('04:00')
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->runInBackground();
