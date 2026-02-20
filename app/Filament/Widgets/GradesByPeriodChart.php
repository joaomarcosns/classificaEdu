<?php

namespace App\Filament\Widgets;

use App\Enums\GradeLevel;
use App\Models\EvaluationPeriod;
use App\Models\Grade;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GradesByPeriodChart extends BaseWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 2;

    public function getTableRecordKey($record): string
    {
        return (string) $record->period_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Grade::query()
                    ->select('period_id', DB::raw('avg(value) as average'), DB::raw('count(*) as total_grades'))
                    ->groupBy('period_id')
                    ->orderBy('period_id')
            )
            ->columns([
                TextColumn::make('period.name')
                    ->label(trans('widgets.grades_by_period.period'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state, $record) => $record->period?->name_label)
                    ->icon('heroicon-o-calendar')
                    ->sortable(),

                TextColumn::make('period.academic_year')
                    ->label('Ano Letivo')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('average')
                    ->label(trans('widgets.grades_by_period.average'))
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 7.0 => 'success',
                        $state >= 5.0 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('total_grades')
                    ->label(trans('widgets.grades_by_period.total_grades'))
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('period_id')
                    ->label(trans('widgets.grades_by_period.period'))
                    ->options(
                        EvaluationPeriod::query()
                            ->orderBy('academic_year')
                            ->orderBy('order')
                            ->get()
                            ->mapWithKeys(fn (EvaluationPeriod $p) => [$p->id => $p->full_label])
                    ),

                SelectFilter::make('grade_level')
                    ->label(trans('widgets.grades_by_period.grade_level'))
                    ->options(fn () => GradeLevel::options())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereIn('period_id', function ($subQuery) use ($data) {
                            $subQuery->select('grades.period_id')
                                ->from('grades')
                                ->join('students', 'grades.student_id', '=', 'students.id')
                                ->where('students.grade_level', $data['value'])
                                ->groupBy('grades.period_id');
                        });
                    }),

                SelectFilter::make('class_name')
                    ->label(trans('widgets.grades_by_period.class_name'))
                    ->options(fn () => Student::query()
                        ->whereNotNull('class_name')
                        ->distinct()
                        ->orderBy('class_name')
                        ->pluck('class_name', 'class_name'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereIn('period_id', function ($subQuery) use ($data) {
                            $subQuery->select('grades.period_id')
                                ->from('grades')
                                ->join('students', 'grades.student_id', '=', 'students.id')
                                ->where('students.class_name', $data['value'])
                                ->groupBy('grades.period_id');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewGrades')
                    ->label(trans('widgets.grades_by_period.view_grades'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('primary')
                    ->tooltip(trans('widgets.grades_by_period.view_grades_tooltip'))
                    ->url(fn ($record): string => route('filament.admin.resources.grades.index', [
                        'tableFilters' => [
                            'period_id' => ['value' => $record->period_id],
                        ],
                    ])),
            ])
            ->paginated(false);
    }

    public function getHeading(): ?string
    {
        return trans('widgets.grades_by_period.heading');
    }
}
