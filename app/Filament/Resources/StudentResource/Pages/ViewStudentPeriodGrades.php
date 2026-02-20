<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Enums\AssessmentType;
use App\Filament\Resources\StudentResource;
use App\Models\EvaluationPeriod;
use App\Models\Grade;
use App\Models\Student;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ViewStudentPeriodGrades extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.view-student-period-grades';

    public Student $record;

    public int $periodId;

    public EvaluationPeriod $period;

    public function mount(Student $record, int $periodId): void
    {
        $this->record = $record;
        $this->period = EvaluationPeriod::findOrFail($periodId);
        $this->periodId = $periodId;
    }

    public function getTitle(): string|Htmlable
    {
        return "{$this->record->name} — {$this->period->full_label}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            StudentResource::getUrl('index') => trans('students.plural_label'),
            StudentResource::getUrl('view', ['record' => $this->record]) => $this->record->name,
            '' => $this->period->name,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('backToStudent')
                ->label(trans('actions.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(StudentResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Grade::query()
                    ->where('student_id', $this->record->id)
                    ->where('period_id', $this->period->id)
                    ->orderBy('evaluation_date', 'desc')
            )
            ->heading(trans('grades.plural_label'))
            ->columns([
                Tables\Columns\TextColumn::make('assessment_type')
                    ->label(trans('grades.fields.assessment_type'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? (AssessmentType::tryFrom($state)?->label() ?? $state)
                        : '—'
                    ),

                Tables\Columns\TextColumn::make('value')
                    ->label(trans('grades.fields.value'))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state < 6.0 => 'danger',
                        $state < 8.0 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),

                Tables\Columns\TextColumn::make('evaluation_date')
                    ->label(trans('grades.fields.evaluation_date'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(trans('grades.fields.notes'))
                    ->limit(60)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(trans('grades.actions.add_grade'))
                    ->model(Grade::class)
                    ->form($this->gradeFormSchema())
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, [
                        'student_id' => $this->record->id,
                        'period_id' => $this->period->id,
                    ])),
            ])
            ->actions([
                EditAction::make()
                    ->label('')
                    ->tooltip(trans('actions.edit'))
                    ->form($this->gradeFormSchema(withPeriod: true)),
                DeleteAction::make()
                    ->label('')
                    ->tooltip(trans('actions.delete')),
            ])
            ->emptyStateHeading(trans('grades.empty_state.heading'))
            ->emptyStateDescription(trans('grades.empty_state.description'));
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    protected function gradeFormSchema(bool $withPeriod = false): array
    {
        $fields = [];

        if ($withPeriod) {
            $fields[] = Forms\Components\Select::make('period_id')
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
                ->native(false);
        }

        $fields[] = Forms\Components\Select::make('assessment_type')
            ->label(trans('grades.fields.assessment_type'))
            ->options(AssessmentType::options())
            ->searchable()
            ->native(false);

        $fields[] = Forms\Components\TextInput::make('value')
            ->label(trans('grades.fields.value'))
            ->required()
            ->numeric()
            ->minValue(0)
            ->maxValue(10)
            ->step(0.01)
            ->suffix('/10');

        $fields[] = Forms\Components\DatePicker::make('evaluation_date')
            ->label(trans('grades.fields.evaluation_date'))
            ->required()
            ->default(now())
            ->native(false)
            ->displayFormat('d/m/Y')
            ->maxDate(now());

        $fields[] = Forms\Components\Textarea::make('notes')
            ->label(trans('grades.fields.notes'))
            ->rows(3)
            ->columnSpanFull();

        return $fields;
    }
}
