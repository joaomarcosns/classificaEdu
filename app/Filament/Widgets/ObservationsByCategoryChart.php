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

class ObservationsByCategoryChart extends BaseWidget
{
    protected static ?string $heading = 'Tabela: Observacoes por categoria';

    protected static ?int $sort = 3;

    public function getTableRecordKey($record): string
    {
        return (string) $record->category;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Observation::query()
                    ->select('category', DB::raw('count(*) as total'))
                    ->groupBy('category')
                    ->orderByDesc('total')
            )
            ->columns([
                TextColumn::make('category')
                    ->label('Categoria')
                    ->formatStateUsing(fn ($state) => trans("observations.categories.{$state}"))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'academic' => 'info',
                        'behavioral' => 'warning',
                        'social' => 'success',
                        'health' => 'danger',
                        default => 'gray',
                    })
                    ->icon('heroicon-o-tag')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('sentiment')
                    ->label('Sentimento')
                    ->options(fn () => trans('observations.sentiments'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereIn('category', function ($subQuery) use ($data) {
                            $subQuery->select('category')
                                ->from('observations')
                                ->where('sentiment', $data['value'])
                                ->groupBy('category');
                        });
                    }),

                SelectFilter::make('grade_level')
                    ->label('Serie')
                    ->options(fn () => trans('students.grade_levels'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereIn('category', function ($subQuery) use ($data) {
                            $subQuery->select('observations.category')
                                ->from('observations')
                                ->join('students', 'observations.student_id', '=', 'students.id')
                                ->where('students.grade_level', $data['value'])
                                ->groupBy('observations.category');
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

                        return $query->whereIn('category', function ($subQuery) use ($data) {
                            $subQuery->select('observations.category')
                                ->from('observations')
                                ->join('students', 'observations.student_id', '=', 'students.id')
                                ->where('students.class_name', $data['value'])
                                ->groupBy('observations.category');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewObservations')
                    ->label('')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->tooltip(trans('actions.view_observations'))
                    ->url(fn ($record): string => route('filament.admin.resources.observations.index', [
                        'tableFilters' => [
                            'category' => ['value' => $record->category],
                        ],
                    ])),
            ])
            ->paginated(false);
    }
}
