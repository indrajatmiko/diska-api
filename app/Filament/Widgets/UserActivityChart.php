<?php
namespace App\Filament\Widgets;
use App\Models\UserActivity;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Aktivitas Pengguna (7 Hari Terakhir)';

    protected function getData(): array
    {
        $data = UserActivity::select(DB::raw('DATE(created_at) as date'), 'event_type', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date', 'event_type')
            ->orderBy('date', 'asc')
            ->get();

        // Proses data untuk format chart
        $labels = $data->pluck('date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d M'))->unique()->values()->all();
        $datasets = [];
        $eventTypes = $data->pluck('event_type')->unique()->values();

        foreach ($eventTypes as $eventType) {
            $dataset = [
                'label' => ucwords(str_replace('_', ' ', $eventType)),
                'data' => [],
            ];
            foreach ($labels as $label) {
                $date = \Carbon\Carbon::createFromFormat('d M', $label)->format('Y-m-d');
                $count = $data->where('date', $date)->where('event_type', $eventType)->first()?->count ?? 0;
                $dataset['data'][] = $count;
            }
            $datasets[] = $dataset;
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string { return 'bar'; } // Bar chart lebih cocok untuk perbandingan
}