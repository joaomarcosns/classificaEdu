<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_pdf')
                ->label('Gerar PDF')
                ->icon('heroicon-o-document')
                ->color('info')
                ->url(fn(Student $record): string => route('students.pdf.download', $record)),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
