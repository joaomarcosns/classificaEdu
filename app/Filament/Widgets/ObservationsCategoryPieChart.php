<?php

namespace App\Filament\Widgets;

use App\Models\Observation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ObservationsCategoryPieChart extends ChartWidget
{
    protected static ?string $heading = 'Grafico: Observacoes por categoria';

    protected static ?int $sort = 6;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $observations = Observation::query()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $labels = $observations->map(function ($row) {
            $labelKey = "observations.categories.{$row->category}";
            $label = trans($labelKey);

            return $label === $labelKey ? $row->category : $label;
        })->all();

        $data = $observations->map(fn ($row) => (int) $row->total)->all();

        return [
            'datasets' => [
                [
                    'label' => 'Observacoes',
                    'data' => $data,
                    'backgroundColor' => [
                        '#60a5fa',
                        '#34d399',
                        '#f59e0b',
                        '#f87171',
                        '#a78bfa',
                        '#9ca3af',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
