<?php

namespace App\Enums;

enum GradeLevel: string
{
    case Year1 = 'year_1';
    case Year2 = 'year_2';
    case Year3 = 'year_3';
    case Year4 = 'year_4';
    case Year5 = 'year_5';
    case Year6 = 'year_6';
    case Year7 = 'year_7';
    case Year8 = 'year_8';
    case Year9 = 'year_9';

    public function label(): string
    {
        return trans("students.grade_levels.{$this->value}");
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
