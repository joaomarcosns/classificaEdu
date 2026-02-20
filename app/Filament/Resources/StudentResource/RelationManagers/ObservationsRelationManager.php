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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class ObservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'observations';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return trans('observations.plural_label');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('observation_date')
                    ->label(trans('observations.fields.observation_date'))
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()),

                Forms\Components\Select::make('category')
                    ->label(trans('observations.fields.category'))
                    ->options(trans('observations.categories'))
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->options(trans('observations.sentiments'))
                    ->required()
                    ->native(false),

                Forms\Components\RichEditor::make('description')
                    ->label(trans('observations.fields.description'))
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'orderedList',
                    ])
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_private')
                    ->label(trans('observations.fields.is_private'))
                    ->helperText(trans('observations.help.is_private'))
                    ->default(false),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('observation_date')
                    ->label(trans('observations.fields.observation_date'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label(trans('observations.fields.category'))
                    ->formatStateUsing(fn($state) => trans("observations.categories.{$state}"))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'comportamento' => 'info',
                        'participacao' => 'success',
                        'cooperacao' => 'primary',
                        'responsabilidade' => 'warning',
                        'interacao_social' => 'indigo',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->icon(fn($state) => match ($state) {
                        'positivo' => 'heroicon-o-check-circle',
                        'neutro' => 'heroicon-o-minus-circle',
                        'preocupante' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn($state) => match ($state) {
                        'positivo' => 'success',
                        'neutro' => 'gray',
                        'preocupante' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label(trans('observations.fields.description'))
                    ->html()
                    ->limit(80)
                    ->tooltip(fn($state) => strip_tags($state)),

                Tables\Columns\IconColumn::make('is_private')
                    ->label(trans('observations.fields.is_private'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(trans('observations.fields.category'))
                    ->options(trans('observations.categories')),

                Tables\Filters\SelectFilter::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->options(trans('observations.sentiments')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->tooltip('Criar'),
            ])
            ->actions([
                ViewAction::make()
                    ->tooltip('Visualizar'),
                EditAction::make()
                    ->tooltip('Editar'),
                DeleteAction::make()
                    ->tooltip('Excluir'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->tooltip('Excluir selecionados'),
                ]),
            ])
            ->defaultSort('observation_date', 'desc');
    }
}
