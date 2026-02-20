<?php

namespace App\Filament\Widgets;

use App\Models\Observation;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentsWithMostObservationsChart extends BaseWidget
{
    protected static ?string $heading = 'Tabela: Alunos com mais observacoes';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->select('students.*', DB::raw('COUNT(observations.id) as observations_count'))
                    ->leftJoin('observations', 'students.id', '=', 'observations.student_id')
                    ->groupBy('students.id')
                    ->havingRaw('COUNT(observations.id) > 0')
                    ->orderBy(DB::raw('COUNT(observations.id)'), 'desc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('grade_level')
                    ->label('Serie')
                    ->formatStateUsing(fn ($state) => trans("students.grade_levels.{$state}"))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('class_name')
                    ->label('Turma')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('observations_count')
                    ->label('Total de Observacoes')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options(fn () => trans('observations.categories'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas(
                                'observations',
                                fn (Builder $query) => $query->where('category', $value)
                            ),
                        );
                    }),

                SelectFilter::make('sentiment')
                    ->label('Sentimento')
                    ->options(fn () => trans('observations.sentiments'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas(
                                'observations',
                                fn (Builder $query) => $query->where('sentiment', $value)
                            ),
                        );
                    }),

                SelectFilter::make('grade_level')
                    ->label('Serie')
                    ->options(fn () => trans('students.grade_levels')),

                SelectFilter::make('class_name')
                    ->label('Turma')
                    ->options(fn () => Student::query()
                        ->whereNotNull('class_name')
                        ->distinct()
                        ->orderBy('class_name')
                        ->pluck('class_name', 'class_name')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver Detalhes')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->tooltip('Visualizar detalhes do aluno')
                    ->url(fn (Student $record): string => route('filament.admin.resources.students.view', ['record' => $record])),
            ])
            ->defaultPaginationPageOption(10)
            ->poll('30s');
    }
}
