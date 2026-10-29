<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('machinery:check-expiries')->dailyAt('06:00')->timezone('Asia/Kolkata');
