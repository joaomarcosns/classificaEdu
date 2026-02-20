<?php

namespace App\Filament\Resources\EvaluationPeriodResource\Pages;

use App\Filament\Resources\EvaluationPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationPeriods extends ListRecords
{
    protected static string $resource = EvaluationPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
