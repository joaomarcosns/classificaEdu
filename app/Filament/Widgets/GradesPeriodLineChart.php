<?php

namespace App\Filament\Widgets;

use App\Models\EvaluationPeriod;
use Filament\Widgets\ChartWidget;

class GradesPeriodLineChart extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 7;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $periods = EvaluationPeriod::query()
            ->whereHas('grades')
            ->withAvg('grades as average', 'value')
            ->orderBy('academic_year')
            ->orderBy('order')
            ->get();

        $labels = $periods->map(fn (EvaluationPeriod $period) => $period->full_label)->all();
        $data = $periods->map(fn (EvaluationPeriod $period) => round((float) $period->average, 2))->all();

        return [
            'datasets' => [
                [
                    'label' => trans('widgets.grades_period_line_chart.dataset_label'),
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

    public function getHeading(): ?string
    {
        return trans('widgets.grades_period_line_chart.heading');
    }
}
