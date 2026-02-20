<?php

namespace App\Filament\Widgets;

use App\Enums\GradeLevel;
use App\Enums\ObservationCategory;
use App\Enums\ObservationSentiment;
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
    protected static ?string $heading = null;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

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
                    ->label(trans('students.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('grade_level')
                    ->label(trans('widgets.students_with_most_observations.grade_level'))
                    ->formatStateUsing(fn ($state, $record) => $record->grade_level_label)
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('class_name')
                    ->label(trans('widgets.students_with_most_observations.class_name'))
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('observations_count')
                    ->label(trans('widgets.students_with_most_observations.total_observations'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(trans('observations.fields.category'))
                    ->options(fn () => ObservationCategory::options())
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
                    ->label(trans('observations.fields.sentiment'))
                    ->options(fn () => ObservationSentiment::options())
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
                    ->label(trans('widgets.students_with_most_observations.grade_level'))
                    ->options(fn () => GradeLevel::options()),

                SelectFilter::make('class_name')
                    ->label(trans('widgets.students_with_most_observations.class_name'))
                    ->options(fn () => Student::query()
                        ->whereNotNull('class_name')
                        ->distinct()
                        ->orderBy('class_name')
                        ->pluck('class_name', 'class_name')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(trans('widgets.students_with_most_observations.view_details'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->tooltip(trans('widgets.students_with_most_observations.view_student_tooltip'))
                    ->url(fn (Student $record): string => route('filament.admin.resources.students.view', ['record' => $record])),
            ])
            ->defaultPaginationPageOption(10)
            ->poll('30s');
    }

    public function getHeading(): ?string
    {
        return trans('widgets.students_with_most_observations.heading');
    }
}
