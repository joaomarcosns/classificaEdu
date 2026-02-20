<?php

namespace App\Models;

use App\Enums\ObservationCategory;
use App\Enums\ObservationSentiment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observation extends Model
{
    /** @use HasFactory<\Database\Factories\ObservationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'user_id',
        'observation_date',
        'category',
        'sentiment',
        'description',
        'is_private',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'observation_date' => 'date',
            'is_private' => 'boolean',
            'category' => ObservationCategory::class,
            'sentiment' => ObservationSentiment::class,
        ];
    }

    /**
     * Get the student that owns the observation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user that created the observation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include public observations.
     */
    public function scopePublic(Builder $query): void
    {
        $query->where('is_private', false);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, ObservationCategory|string $category): void
    {
        $query->where('category', $category instanceof ObservationCategory ? $category->value : $category);
    }

    /**
     * Scope a query to filter by sentiment.
     */
    public function scopeBySentiment(Builder $query, ObservationSentiment|string $sentiment): void
    {
        $query->where('sentiment', $sentiment instanceof ObservationSentiment ? $sentiment->value : $sentiment);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('observation_date', [$start, $end]);
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category instanceof ObservationCategory) {
            return $this->category->label();
        }

        return ObservationCategory::tryFrom((string) $this->category)?->label()
            ?? trans("observations.categories.{$this->category}");
    }

    public function getSentimentLabelAttribute(): string
    {
        if ($this->sentiment instanceof ObservationSentiment) {
            return $this->sentiment->label();
        }

        return ObservationSentiment::tryFrom((string) $this->sentiment)?->label()
            ?? trans("observations.sentiments.{$this->sentiment}");
    }
}
