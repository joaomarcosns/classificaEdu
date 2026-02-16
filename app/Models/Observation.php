<?php

namespace App\Models;

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
    public function scopeByCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }

    /**
     * Scope a query to filter by sentiment.
     */
    public function scopeBySentiment(Builder $query, string $sentiment): void
    {
        $query->where('sentiment', $sentiment);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('observation_date', [$start, $end]);
    }

    /**
     * Get the pt-BR category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return trans("observations.categories.{$this->category}");
    }

    /**
     * Get the pt-BR sentiment label.
     */
    public function getSentimentLabelAttribute(): string
    {
        return trans("observations.sentiments.{$this->sentiment}");
    }
}
