<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'registration_number',
        'date_of_birth',
        'grade_level',
        'class_name',
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
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the grades for the student.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the observations for the student.
     */
    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    /**
     * Get the student's classification.
     */
    public function classification(): HasOne
    {
        return $this->hasOne(StudentClassification::class);
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by grade level.
     */
    public function scopeByGradeLevel(Builder $query, string $level): void
    {
        $query->where('grade_level', $level);
    }

    /**
     * Scope a query to filter by class name.
     */
    public function scopeByClass(Builder $query, string $className): void
    {
        $query->where('class_name', $className);
    }

    /**
     * Get the student's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    /**
     * Calculate the student's current average for a given period.
     */
    public function calculateCurrentAverage(?string $period = null): float
    {
        $query = $this->grades();

        if ($period) {
            $query->where('evaluation_period', $period);
        }

        return (float) $query->avg('value') ?? 0.0;
    }

    /**
     * Get the student's classification level.
     */
    public function getClassificationLevel(): string
    {
        return $this->classification?->classification_level ?? 'basico';
    }

    /**
     * Check if student has grade for a given period.
     */
    public function hasGradeForPeriod(string $period): bool
    {
        return $this->grades()->where('evaluation_period', $period)->exists();
    }
}
