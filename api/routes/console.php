<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('leads:digest')->dailyAt('09:00')->timezone('Asia/Dubai');
Schedule::command('leads:digest --overdue')->everyThreeHours()->between('08:00', '20:00')->timezone('Asia/Dubai');
Schedule::command('queue:prune-failed --hours=168')->daily();
