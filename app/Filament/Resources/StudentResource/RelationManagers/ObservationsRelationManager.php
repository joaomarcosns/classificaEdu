<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enums\ObservationCategory;
use App\Enums\ObservationSentiment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
                    ->options(ObservationCategory::options())
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->options(ObservationSentiment::options())
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
                    }),

                Tables\Columns\IconColumn::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->icon(function ($state): string {
                        if ($state instanceof ObservationSentiment) {
                            return $state->icon();
                        }

                        return ObservationSentiment::tryFrom((string) $state)?->icon() ?? 'heroicon-o-question-mark-circle';
                    })
                    ->color(function ($state): string {
                        if ($state instanceof ObservationSentiment) {
                            return $state->color();
                        }

                        return ObservationSentiment::tryFrom((string) $state)?->color() ?? 'gray';
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label(trans('observations.fields.description'))
                    ->html()
                    ->limit(80)
                    ->tooltip(fn ($state) => strip_tags($state)),

                Tables\Columns\IconColumn::make('is_private')
                    ->label(trans('observations.fields.is_private'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(trans('observations.fields.category'))
                    ->options(ObservationCategory::options()),

                Tables\Filters\SelectFilter::make('sentiment')
                    ->label(trans('observations.fields.sentiment'))
                    ->options(ObservationSentiment::options()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->tooltip(trans('actions.create')),
            ])
            ->actions([
                ViewAction::make()
                    ->tooltip(trans('actions.view')),
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
            ->defaultSort('observation_date', 'desc');
    }
}
