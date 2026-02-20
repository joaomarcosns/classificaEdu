<?php

namespace App\Filament\Resources;

use App\Enums\AssessmentType;
use App\Filament\Resources\GradeResource\Pages;
use App\Models\EvaluationPeriod;
use App\Models\Grade;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return trans('grades.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('grades.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return trans('grades.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('grades.navigation_group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->label(trans('grades.fields.student'))
                    ->options(Student::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(trans('grades.fields.student'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.registration_number')
                    ->label(trans('students.fields.registration_number'))
                    ->searchable(),

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
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Tables\Filters\Filter::make('value_range')
                    ->form([
                        Forms\Components\Select::make('range')
                            ->options([
                                'low' => trans('grades.ranges.low'),
                                'medium' => trans('grades.ranges.medium'),
                                'high' => trans('grades.ranges.high'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['range'] === 'low',
                            fn ($query) => $query->where('value', '<', 6.0)
                        )->when(
                            $data['range'] === 'medium',
                            fn ($query) => $query->whereBetween('value', [6.0, 7.9])
                        )->when(
                            $data['range'] === 'high',
                            fn ($query) => $query->where('value', '>=', 8.0)
                        );
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('')
                    ->tooltip(trans('actions.edit')),
                DeleteAction::make()
                    ->label('')
                    ->tooltip(trans('actions.delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('')
                        ->tooltip(trans('actions.delete_selected')),
                ]),
            ])
            ->defaultSort('evaluation_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
