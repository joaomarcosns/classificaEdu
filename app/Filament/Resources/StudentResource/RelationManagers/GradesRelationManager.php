<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enums\AssessmentType;
use App\Models\EvaluationPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return trans('grades.plural_label');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->searchable()
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('period.name')
                    ->label(trans('grades.fields.evaluation_period'))
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $record->period?->name_label)
                    ->sortable(),

                Tables\Columns\TextColumn::make('period.academic_year')
                    ->label(trans('evaluation_periods.fields.academic_year'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assessment_type')
                    ->label(trans('grades.fields.assessment_type'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(function (?string $state): ?string {
                        if (! $state) {
                            return null;
                        }

                        return AssessmentType::tryFrom($state)?->label() ?? $state;
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('value')
                    ->label(trans('grades.fields.value'))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state < 6.0 => 'danger',
                        $state < 8.0 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('evaluation_date')
                    ->label(trans('grades.fields.evaluation_date'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(trans('grades.fields.notes'))
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period_id')
                    ->label(trans('grades.fields.evaluation_period'))
                    ->options(
                        EvaluationPeriod::query()
                            ->orderBy('academic_year')
                            ->orderBy('order')
                            ->get()
                            ->mapWithKeys(fn (EvaluationPeriod $p) => [$p->id => $p->full_label])
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->tooltip(trans('actions.create')),
            ])
            ->actions([
                EditAction::make()
                    ->tooltip(trans('actions.edit')),
                DeleteAction::make()
                    ->tooltip(trans('actions.delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->tooltip(trans('actions.delete_selected')),
                ]),
            ])
            ->defaultSort('evaluation_date', 'desc');
    }
}
