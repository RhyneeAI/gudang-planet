<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clean old reports every day at 2 AM
Schedule::command('reports:clean --days=3')->dailyAt('02:00');
Schedule::command('telescope:prune --hours=48')->daily();