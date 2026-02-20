<?php

namespace App\Enums;

enum ObservationCategory: string
{
    case Behavior = 'behavior';
    case Participation = 'participation';
    case Cooperation = 'cooperation';
    case Responsibility = 'responsibility';
    case SocialInteraction = 'social_interaction';
    case Other = 'other';

    public function label(): string
    {
        return trans("observations.categories.{$this->value}");
    }

    public function impactSkillLabel(): string
    {
        return trans("reports.impact.skills.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::Behavior => 'info',
            self::Participation => 'success',
            self::Cooperation => 'primary',
            self::Responsibility => 'warning',
            self::SocialInteraction => 'indigo',
            self::Other => 'gray',
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
