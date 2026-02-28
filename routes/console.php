<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alertes quotidiennes : visas qui expirent (J-30), soldes impayés (J-15)
Schedule::command('visas:expiring', ['--days' => 30])->dailyAt('08:00');
Schedule::command('payments:reminder', ['--days' => 15])->dailyAt('08:00');
