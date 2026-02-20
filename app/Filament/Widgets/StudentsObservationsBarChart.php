<?php

namespace App\Filament\Widgets;

use App\Models\Observation;
use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StudentsObservationsBarChart extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $observations = Observation::query()
            ->select('student_id', DB::raw('count(*) as total'))
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        $studentNames = Student::query()
            ->whereIn('id', $observations->pluck('student_id'))
            ->pluck('name', 'id');

        $labels = $observations->map(function ($row) use ($studentNames) {
            return $studentNames[$row->student_id] ?? trans('widgets.students_observations_bar.student_fallback', [
                'id' => $row->student_id,
            ]);
        })->all();

        $data = $observations->map(fn ($row) => (int) $row->total)->all();

        return [
            'datasets' => [
                [
                    'label' => trans('widgets.students_observations_bar.dataset_label'),
                    'data' => $data,
                    'backgroundColor' => '#60a5fa',
                    'borderColor' => '#60a5fa',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    public function getHeading(): ?string
    {
        return trans('widgets.students_observations_bar.heading');
    }
}
