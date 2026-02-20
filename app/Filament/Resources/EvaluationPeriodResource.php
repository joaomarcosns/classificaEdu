<?php

namespace App\Filament\Resources;

use App\Enums\EvaluationPeriodName;
use App\Filament\Resources\EvaluationPeriodResource\Pages;
use App\Models\EvaluationPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class EvaluationPeriodResource extends Resource
{
    protected static ?string $model = EvaluationPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return trans('evaluation_periods.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('evaluation_periods.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return trans('evaluation_periods.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('evaluation_periods.navigation_group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('academic_year')
                    ->label(trans('evaluation_periods.fields.academic_year'))
                    ->required()
                    ->maxLength(10),

                Forms\Components\Select::make('name')
                    ->label(trans('evaluation_periods.fields.name'))
                    ->required()
                    ->options(EvaluationPeriodName::options())
                    ->searchable()
                    ->native(false),

                Forms\Components\TextInput::make('order')
                    ->label(trans('evaluation_periods.fields.order'))
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->default(1),

                Forms\Components\Toggle::make('is_active')
                    ->label(trans('evaluation_periods.fields.is_active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academic_year')
                    ->label(trans('evaluation_periods.fields.academic_year'))
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(trans('evaluation_periods.fields.name'))
                    ->formatStateUsing(function ($state): string {
                        if ($state instanceof EvaluationPeriodName) {
                            return $state->label();
                        }

                        return EvaluationPeriodName::tryFrom((string) $state)?->label() ?? (string) $state;
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(trans('evaluation_periods.fields.order'))
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(trans('evaluation_periods.fields.is_active'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('grades_count')
                    ->label(trans('evaluation_periods.fields.grades_count'))
                    ->counts('grades')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year')
                    ->label(trans('evaluation_periods.fields.academic_year'))
                    ->options(fn () => EvaluationPeriod::query()
                        ->distinct()
                        ->orderBy('academic_year')
                        ->pluck('academic_year', 'academic_year')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans('evaluation_periods.fields.is_active')),
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
            ->defaultSort('academic_year')
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluationPeriods::route('/'),
            'create' => Pages\CreateEvaluationPeriod::route('/create'),
            'edit' => Pages\EditEvaluationPeriod::route('/{record}/edit'),
        ];
    }
}
