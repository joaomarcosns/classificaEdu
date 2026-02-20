<?php

namespace App\Enums;

enum EvaluationPeriodName: string
{
    case FirstTerm = 'first_term';
    case SecondTerm = 'second_term';
    case ThirdTerm = 'third_term';

    public function label(): string
    {
        return trans("evaluation_periods.names.{$this->value}");
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
