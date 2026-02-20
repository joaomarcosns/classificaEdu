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
            Stat::make(trans('widgets.students_overview.students'), Student::query()->count()),
            Stat::make(trans('widgets.students_overview.active_students'), Student::query()->where('is_active', true)->count()),
            Stat::make(trans('widgets.students_overview.grades'), Grade::query()->count()),
            Stat::make(trans('widgets.students_overview.observations'), Observation::query()->count()),
            Stat::make(trans('widgets.students_overview.average_grade'), number_format($averageGrade, 2)),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
