<?php

namespace App\Filament\Widgets;

use App\Models\ApiLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ApiStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->today();
        
        // =========================================================
        // ==> PERBAIKAN UTAMA DAN FINAL ADA DI SINI <==
        // =========================================================
        // Ambil nilai rata-rata dan secara paksa ubah menjadi float.
        // Ini akan mengubah null, '', atau nilai non-numerik lainnya menjadi 0.0
        $averageDuration = (float) ApiLog::avg('duration_ms');

        return [
            Stat::make('Total Permintaan (Hari Ini)', ApiLog::whereDate('created_at', $today)->count()),
            Stat::make('Total Error (Hari Ini)', ApiLog::whereDate('created_at', $today)->where('status_code', '>=', 400)->count()),
            Stat::make('Waktu Respons Rata-rata', Number::format($averageDuration, precision: 2) . ' ms'),
        ];
    }
}