<?php

namespace App\Filament\Widgets;

use App\Models\Observation;
use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StudentsObservationsBarChart extends ChartWidget
{
    protected static ?string $heading = 'Grafico: Alunos com mais observacoes';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

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
            return $studentNames[$row->student_id] ?? "Aluno #{$row->student_id}";
        })->all();

        $data = $observations->map(fn ($row) => (int) $row->total)->all();

        return [
            'datasets' => [
                [
                    'label' => 'Observacoes',
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
}
