<?php

namespace App\Models;

use App\Enums\EvaluationPeriodName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationPeriod extends Model
{
    /** @use HasFactory<\Database\Factories\EvaluationPeriodFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'academic_year',
        'name',
        'order',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'name' => EvaluationPeriodName::class,
        ];
    }

    /**
     * Get the grades for this period.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'period_id');
    }

    /**
     * Get a human-readable label combining year and name.
     */
    public function getFullLabelAttribute(): string
    {
        return "{$this->academic_year} — {$this->name_label}";
    }

    /**
     * Get the localized name label.
     */
    public function getNameLabelAttribute(): string
    {
        if ($this->name instanceof EvaluationPeriodName) {
            return $this->name->label();
        }

        return (string) $this->name;
    }
}
