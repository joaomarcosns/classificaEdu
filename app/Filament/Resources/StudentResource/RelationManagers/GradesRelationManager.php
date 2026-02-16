<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
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
                Forms\Components\TextInput::make('value')
                    ->label(trans('grades.fields.value'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10)
                    ->step(0.01)
                    ->suffix('/10'),

                Forms\Components\Select::make('evaluation_period')
                    ->label(trans('grades.fields.evaluation_period'))
                    ->options(trans('grades.periods'))
                    ->required()
                    ->native(false),

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
                Tables\Columns\TextColumn::make('value')
                    ->label(trans('grades.fields.value'))
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state < 6.0 => 'danger',
                        $state < 8.0 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn($state) => number_format($state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('evaluation_period')
                    ->label(trans('grades.fields.evaluation_period'))
                    ->formatStateUsing(fn($state) => trans("grades.periods.{$state}"))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('evaluation_date')
                    ->label(trans('grades.fields.evaluation_date'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(trans('grades.fields.notes'))
                    ->limit(50)
                    ->tooltip(fn($state) => $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('evaluation_period')
                    ->label(trans('grades.fields.evaluation_period'))
                    ->options(trans('grades.periods')),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('evaluation_date', 'desc');
    }
}
