<?php
namespace App\Filament\Widgets;
use App\Models\User;
use App\Models\UserActivity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserAnalyticsOverview extends BaseWidget
{
    protected static ?int $sort = -1; // Tampilkan di atas widget API

    protected function getStats(): array
    {
        $today = now()->today();
        return [
            Stat::make('Total Pengguna Terdaftar', User::count()),
            Stat::make('Pendaftaran Baru (Hari Ini)', User::whereDate('created_at', $today)->count()),
            Stat::make('Total Pesanan Dibuat', UserActivity::where('event_type', 'order_created')->count()),
        ];
    }
}