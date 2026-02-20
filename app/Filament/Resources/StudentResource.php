<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\GradesRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\ObservationsRelationManager;
use App\Models\Student;
use App\Services\ClassificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestão de Alunos';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return trans('students.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('students.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return trans('students.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans('students.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(trans('students.fields.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('registration_number')
                            ->label(trans('students.fields.registration_number'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label(trans('students.fields.date_of_birth'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),

                        Forms\Components\Select::make('grade_level')
                            ->label(trans('students.fields.grade_level'))
                            ->options(trans('students.grade_levels'))
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('class_name')
                            ->label(trans('students.fields.class_name'))
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans('students.fields.is_active'))
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label(trans('students.fields.registration_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(trans('students.fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('grade_level')
                    ->label(trans('students.fields.grade_level'))
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('class_name')
                    ->label(trans('students.fields.class_name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('classification.level_label')
                    ->label(trans('students.sections.classification'))
                    ->badge()
                    ->color(fn($record) => $record->classification?->level_color ?? 'gray')
                    ->default('N/A'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans('students.fields.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans('students.fields.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_level')
                    ->label(trans('students.fields.grade_level'))
                    ->options(trans('students.grade_levels')),

                Tables\Filters\SelectFilter::make('class_name')
                    ->label(trans('students.fields.class_name'))
                    ->options(fn() => Student::query()->distinct()->pluck('class_name', 'class_name')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans('students.fields.is_active'))
                    ->trueLabel(trans('students.filters.active'))
                    ->falseLabel(trans('students.filters.inactive'))
                    ->native(false),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('')
                    ->tooltip(trans('actions.view')),
                EditAction::make()
                    ->label('')
                    ->tooltip(trans('actions.edit')),
                DeleteAction::make()
                    ->label('')
                    ->tooltip(trans('actions.delete')),

                Action::make('recalculate')
                    ->label('') 
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Student $record) {
                        app(ClassificationService::class)->classifyStudent($record);

                        Notification::make()
                            ->success()
                            ->title(trans('students.messages.classification_recalculated'))
                            ->send();
                    })->tooltip(trans('students.actions.recalculate')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->tooltip(trans('actions.delete_selected')),

                    BulkAction::make('recalculate_bulk')
                        ->label(trans('actions.recalculate_bulk'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->action(function ($records) {
                            $service = app(ClassificationService::class);
                            $count = 0;

                            foreach ($records as $student) {
                                $service->classifyStudent($student);
                                $count++;
                            }

                            Notification::make()
                                ->success()
                                ->title(trans('students.messages.classifications_recalculated', ['count' => $count]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->tooltip(trans('actions.recalculate_bulk')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            GradesRelationManager::class,
            ObservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['classification']);
    }
}
