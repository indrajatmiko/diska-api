<?php
namespace App\Filament\Widgets;
use App\Models\ApiLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ApiRequestsChart extends ChartWidget
{
    protected static ?string $heading = 'Permintaan API (7 Hari Terakhir)';

    protected function getData(): array
    {
        $data = ApiLog::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Permintaan per Hari',
                    'data' => $data->pluck('count')->all(),
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d M'))->all(),
        ];
    }
    protected function getType(): string { return 'line'; }
}