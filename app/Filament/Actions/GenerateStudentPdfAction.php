<?php

namespace App\Filament\Actions;

use App\Models\Student;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use function Spatie\LaravelPdf\Support\pdf;

class GenerateStudentPdfAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generate_pdf';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Gerar PDF')
            ->icon('heroicon-o-document')
            ->color('info')
            ->action(fn(Student $record) => $this->handle($record));
    }

    public function handle(Student $record)
    {
        return pdf()
            ->view('docs.pdf.student', ['student' => $record])
            ->name('student_' . Str::slug($record->name) . '.pdf')
            ->download();
    }
}
