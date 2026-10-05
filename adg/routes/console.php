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
 */
Schedule::command('agenda:reminder')
    ->dailyAt('20:00')
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->runInBackground();
