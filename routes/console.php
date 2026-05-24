<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('recurring:process')->dailyAt('00:00');
Schedule::command('approvals:auto-reject')->everyMinute();
Schedule::command('audit:prune --days=90')->daily();
