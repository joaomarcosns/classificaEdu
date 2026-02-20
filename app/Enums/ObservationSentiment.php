<?php

namespace App\Enums;

enum ObservationSentiment: string
{
    case Positive = 'positive';
    case Neutral = 'neutral';
    case Concerning = 'concerning';

    public function label(): string
    {
        return trans("observations.sentiments.{$this->value}");
    }

    public function icon(): string
    {
        return match ($this) {
            self::Positive => 'heroicon-o-check-circle',
            self::Neutral => 'heroicon-o-minus-circle',
            self::Concerning => 'heroicon-o-exclamation-triangle',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Positive => 'success',
            self::Neutral => 'gray',
            self::Concerning => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
