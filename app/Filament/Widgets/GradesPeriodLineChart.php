<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GradesPeriodLineChart extends ChartWidget
{
    protected static ?string $heading = 'Grafico: Media por periodo';

    protected static ?int $sort = 7;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $grades = Grade::query()
            ->select('evaluation_period', DB::raw('avg(value) as average'))
            ->groupBy('evaluation_period')
            ->orderBy('evaluation_period')
            ->get();

        $labels = $grades->map(function ($row) {
            $labelKey = "grades.periods.{$row->evaluation_period}";
            $label = trans($labelKey);

            return $label === $labelKey ? $row->evaluation_period : $label;
        })->all();

        $data = $grades->map(fn ($row) => round((float) $row->average, 2))->all();

        return [
            'datasets' => [
                [
                    'label' => 'Media',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 10,
                ],
            ],
        ];
    }
}
