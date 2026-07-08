<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Lên lịch chạy kiểm tra và tự động hủy hoa hết hạn mỗi ngày lúc 00:00
Schedule::command('app:auto-cancel-expired-flowers')->daily();
