<?php

use App\Jobs\GenerateNicknameJob;
use Illuminate\Support\Facades\Schedule;

// Generate nicknames every minute
Schedule::job(new GenerateNicknameJob())->everyMinute();
