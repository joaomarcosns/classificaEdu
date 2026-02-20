<?php

namespace App\Models;

use App\Enums\ClassificationLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassification extends Model
{
    /** @use HasFactory<\Database\Factories\StudentClassificationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'classification_level',
        'overall_average',
        'evaluation_period',
        'classification_date',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'overall_average' => 'decimal:2',
            'classification_date' => 'datetime',
            'metadata' => 'array',
            'classification_level' => ClassificationLevel::class,
        ];
    }

    /**
     * Get the student that owns the classification.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getLevelLabelAttribute(): string
    {
        if ($this->classification_level instanceof ClassificationLevel) {
            return $this->classification_level->label();
        }

        return ClassificationLevel::tryFrom((string) $this->classification_level)?->label() ?? 'N/A';
    }

    /**
     * Get the badge color for the classification level.
     */
    public function getLevelColorAttribute(): string
    {
        if ($this->classification_level instanceof ClassificationLevel) {
            return $this->classification_level->color();
        }

        return ClassificationLevel::tryFrom((string) $this->classification_level)?->color() ?? 'gray';
    }
}
