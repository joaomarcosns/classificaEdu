<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use App\Models\Observation;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $averageGrade = (float) Grade::query()->avg('value');

        return [
            Stat::make('Alunos', Student::query()->count()),
            Stat::make('Alunos ativos', Student::query()->where('is_active', true)->count()),
            Stat::make('Notas', Grade::query()->count()),
            Stat::make('Observacoes', Observation::query()->count()),
            Stat::make('Media geral', number_format($averageGrade, 2)),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
