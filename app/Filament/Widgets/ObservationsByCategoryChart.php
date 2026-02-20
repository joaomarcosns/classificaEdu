<?php

namespace App\Filament\Widgets;

use App\Enums\GradeLevel;
use App\Enums\ObservationCategory;
use App\Enums\ObservationSentiment;
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
    protected static ?string $heading = null;

    protected static ?int $sort = 3;

    public function getTableRecordKey($record): string
    {
        return $record->category instanceof ObservationCategory
            ? $record->category->value
            : (string) $record->category;
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
                    ->label(trans('observations.fields.category'))
                    ->formatStateUsing(function ($state): string {
                        if ($state instanceof ObservationCategory) {
                            return $state->label();
                        }

                        return ObservationCategory::tryFrom((string) $state)?->label() ?? (string) $state;
                    })
                    ->badge()
                    ->color(function ($state): string {
                        if ($state instanceof ObservationCategory) {
                            return $state->color();
                        }

                        return ObservationCategory::tryFrom((string) $state)?->color() ?? 'gray';
                    })
                    ->icon('heroicon-o-tag')
                    ->sortable(),

                TextColumn::make('total')
                    ->label(trans('widgets.observations_by_category.total'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->options(fn () => ObservationSentiment::options())
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
                    ->label(trans('widgets.observations_by_category.grade_level'))
                    ->options(fn () => GradeLevel::options())
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
                    ->label(trans('widgets.observations_by_category.class_name'))
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
                            'category' => [
                                'value' => $record->category instanceof ObservationCategory
                                    ? $record->category->value
                                    : $record->category,
                            ],
                        ],
                    ])),
            ])
            ->paginated(false);
    }

    public function getHeading(): ?string
    {
        return trans('widgets.observations_by_category.heading');
    }
}
