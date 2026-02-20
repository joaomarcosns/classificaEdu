<?php

namespace App\Enums;

enum ClassificationLevel: string
{
    case Basic = 'basic';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    public function label(): string
    {
        return trans("reports.classifications.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Basic => 'danger',
            self::Intermediate => 'warning',
            self::Advanced => 'success',
        };
    }
}
