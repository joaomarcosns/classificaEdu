<?php

namespace App\Filament\Widgets;

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
    protected static ?string $heading = 'Tabela: Media por periodo';

    protected static ?int $sort = 2;

    public function getTableRecordKey($record): string
    {
        return (string) $record->evaluation_period;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Grade::query()
                    ->select('evaluation_period', DB::raw('avg(value) as average'), DB::raw('count(*) as total_grades'))
                    ->groupBy('evaluation_period')
                    ->orderBy('evaluation_period')
            )
            ->columns([
                TextColumn::make('evaluation_period')
                    ->label('Periodo')
                    ->formatStateUsing(fn ($state) => trans("grades.periods.{$state}"))
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-calendar')
                    ->sortable(),

                TextColumn::make('average')
                    ->label('Media')
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 7.0 => 'success',
                        $state >= 5.0 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('total_grades')
                    ->label('Total de Notas')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('grade_level')
                    ->label('Serie')
                    ->options(fn () => trans('students.grade_levels'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereIn('evaluation_period', function ($subQuery) use ($data) {
                            $subQuery->select('grades.evaluation_period')
                                ->from('grades')
                                ->join('students', 'grades.student_id', '=', 'students.id')
                                ->where('students.grade_level', $data['value'])
                                ->groupBy('grades.evaluation_period');
                        });
                    }),

                SelectFilter::make('class_name')
                    ->label('Turma')
                    ->options(fn () => Student::query()
                        ->whereNotNull('class_name')
                        ->distinct()
                        ->orderBy('class_name')
                        ->pluck('class_name', 'class_name'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereIn('evaluation_period', function ($subQuery) use ($data) {
                            $subQuery->select('grades.evaluation_period')
                                ->from('grades')
                                ->join('students', 'grades.student_id', '=', 'students.id')
                                ->where('students.class_name', $data['value'])
                                ->groupBy('grades.evaluation_period');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewGrades')
                    ->label('Ver Notas')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('primary')
                    ->url(fn ($record): string => route('filament.admin.resources.grades.index', [
                        'tableFilters' => [
                            'evaluation_period' => ['value' => $record->evaluation_period],
                        ],
                    ])),
            ])
            ->paginated(false);
    }
}
