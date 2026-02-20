<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enums\AssessmentType;
use App\Filament\Resources\StudentResource;
use App\Models\EvaluationPeriod;
use App\Models\Grade;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return trans('grades.plural_label');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->period_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Grade::query()
                    ->select('period_id', DB::raw('avg(value) as value'), DB::raw('count(*) as grades_count'))
                    ->where('student_id', $this->ownerRecord->id)
                    ->groupBy('period_id')
                    ->with('period')
                    ->orderBy('period_id')
            )
            ->recordUrl(fn (Grade $record): string => StudentResource::getUrl('view-period-grades', [
                'record' => $this->ownerRecord,
                'periodId' => $record->period_id,
            ]))
            ->columns([
                Tables\Columns\TextColumn::make('period.name')
                    ->label(trans('grades.fields.evaluation_period'))
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state, Grade $record): string => $record->period?->name_label ?? '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period.academic_year')
                    ->label(trans('evaluation_periods.fields.academic_year'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(trans('grades.average'))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state < 6.0 => 'danger',
                        $state < 8.0 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('grades_count')
                    ->label(trans('grades.grades_count'))
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(trans('grades.actions.add_grade'))
                    ->model(Grade::class)
                    ->form([
                        Forms\Components\Select::make('period_id')
                            ->label(trans('grades.fields.evaluation_period'))
                            ->options(
                                EvaluationPeriod::query()
                                    ->where('is_active', true)
                                    ->orderBy('academic_year')
                                    ->orderBy('order')
                                    ->get()
                                    ->mapWithKeys(fn (EvaluationPeriod $p) => [$p->id => $p->full_label])
                            )
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('assessment_type')
                            ->label(trans('grades.fields.assessment_type'))
                            ->options(AssessmentType::options())
                            ->searchable()
                            ->native(false),

                        Forms\Components\TextInput::make('value')
                            ->label(trans('grades.fields.value'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->step(0.01)
                            ->suffix('/10'),

                        Forms\Components\DatePicker::make('evaluation_date')
                            ->label(trans('grades.fields.evaluation_date'))
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),

                        Forms\Components\Textarea::make('notes')
                            ->label(trans('grades.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, [
                        'student_id' => $this->ownerRecord->id,
                    ])),
            ])
            ->actions([
                Action::make('viewGrades')
                    ->label('')
                    ->icon('heroicon-o-arrow-right')
                    ->tooltip(trans('grades.actions.view_grades'))
                    ->url(fn (Grade $record): string => StudentResource::getUrl('view-period-grades', [
                        'record' => $this->ownerRecord,
                        'periodId' => $record->period_id,
                    ])),
            ])
            ->paginated(false);
    }
}
