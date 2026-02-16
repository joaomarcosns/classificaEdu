<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObservationResource\Pages;
use App\Models\Observation;
use App\Models\Student;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestão de Alunos';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return trans('observations.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('observations.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return trans('observations.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->label(trans('observations.fields.student'))
                    ->options(Student::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(trans('observations.fields.student'))
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('user.name')
                    ->label(trans('observations.fields.user'))
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('observation_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObservations::route('/'),
            'create' => Pages\CreateObservation::route('/create'),
            'view' => Pages\ViewObservation::route('/{record}'),
            'edit' => Pages\EditObservation::route('/{record}/edit'),
        ];
    }
}
