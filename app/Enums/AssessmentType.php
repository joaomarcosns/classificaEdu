<?php

namespace App\Enums;

enum AssessmentType: string
{
    case Exam = 'exam';
    case Assignment = 'assignment';
    case Participation = 'participation';
    case Exercise = 'exercise';
    case Project = 'project';

    public function label(): string
    {
        return trans("grades.assessment_types.{$this->value}");
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
