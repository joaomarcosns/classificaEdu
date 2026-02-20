<?php

namespace App\Filament\Actions;

use App\Models\Student;
use Filament\Actions\Action;

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
            ->label(trans('actions.generate_pdf'))
            ->icon('heroicon-o-document')
            ->color('info')
            ->url(fn (Student $record): string => route('students.pdf.download', $record));
    }
}
