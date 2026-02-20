<?php

namespace App\Services;

use App\Enums\ObservationCategory;
use App\Enums\ObservationSentiment;
use App\Models\Student;

class ReportService
{
    /**
     * Generate comprehensive student report data.
     */
    public function generateStudentReport(Student $student, ?int $periodId = null): array
    {
        $student->load(['grades.period', 'observations.user', 'classification']);

        return [
            'student' => $student,
            'classification' => $student->classification,
            'grades' => $this->getGradesData($student, $periodId),
            'observations' => $this->getObservationsData($student),
            'impact_analysis' => $this->generateImpactAnalysis($student),
            'generated_at' => now(),
            'period_id' => $periodId,
        ];
    }

    /**
     * Get grades data with period breakdown.
     */
    protected function getGradesData(Student $student, ?int $periodId): array
    {
        $query = $student->grades()->with('period');

        if ($periodId) {
            $query->where('period_id', $periodId);
        }

        $grades = $query->orderBy('evaluation_date')->get();

        $byPeriod = $grades->groupBy('period_id')->map(function ($periodGrades) {
            return [
                'period' => $periodGrades->first()->period,
                'grades' => $periodGrades,
                'average' => $periodGrades->avg('value'),
                'classification' => $this->getClassificationFromAverage($periodGrades->avg('value')),
            ];
        });

        return [
            'all' => $grades,
            'by_period' => $byPeriod,
            'overall_average' => $grades->avg('value') ?? 0,
        ];
    }

    /**
     * Get observations data grouped by category.
     */
    protected function getObservationsData(Student $student): array
    {
        $observations = $student->observations()
            ->with('user')
            ->public()
            ->orderBy('observation_date', 'desc')
            ->get();

        $byCategory = $observations->groupBy(function ($observation) {
            return $observation->category instanceof ObservationCategory
                ? $observation->category->value
                : $observation->category;
        });
        $bySentiment = $observations->groupBy(function ($observation) {
            return $observation->sentiment instanceof ObservationSentiment
                ? $observation->sentiment->value
                : $observation->sentiment;
        });

        return [
            'all' => $observations,
            'by_category' => $byCategory,
            'by_sentiment' => $bySentiment,
            'total_count' => $observations->count(),
            'category_counts' => $byCategory->map->count(),
            'sentiment_counts' => $bySentiment->map->count(),
        ];
    }

    /**
     * Generate impact analysis from observations.
     */
    public function generateImpactAnalysis(Student $student): array
    {
        $observations = $student->observations()->public()->get();
        $analysis = [];

        $grouped = $observations->groupBy(function ($observation) {
            return $observation->category instanceof ObservationCategory
                ? $observation->category->value
                : $observation->category;
        });

        foreach ($grouped as $category => $categoryObservations) {
            $sentiments = $categoryObservations->groupBy(function ($observation) {
                return $observation->sentiment instanceof ObservationSentiment
                    ? $observation->sentiment->value
                    : $observation->sentiment;
            });

            $insights = [];

            foreach ($sentiments as $sentiment => $sentimentObservations) {
                $count = $sentimentObservations->count();
                $skill = ObservationCategory::tryFrom($category)?->impactSkillLabel()
                    ?? trans('reports.impact.skills.general_development');

                $insight = $this->buildInsight($count, $category, $sentiment, $skill);

                if ($insight) {
                    $insights[] = $insight;
                }
            }

            if (! empty($insights)) {
                $analysis[$category] = [
                    'category_label' => trans("observations.categories.{$category}"),
                    'skill' => ObservationCategory::tryFrom($category)?->impactSkillLabel()
                        ?? trans('reports.impact.skills.general_development'),
                    'insights' => $insights,
                    'total_count' => $categoryObservations->count(),
                ];
            }
        }

        return $analysis;
    }

    /**
     * Build insight text based on observation data.
     */
    protected function buildInsight(int $count, string $category, string $sentiment, string $skill): ?string
    {
        $categoryLabel = trans("observations.categories.{$category}");

        return match ($sentiment) {
            ObservationSentiment::Positive->value => sprintf(
                '%s %s %s',
                trans('reports.impact.positive_prefix', ['count' => $count, 'category' => $categoryLabel]),
                trans('reports.impact.positive_impact'),
                trans('reports.impact.skill_suffix', ['skill' => $skill])
            ),

            ObservationSentiment::Concerning->value => sprintf(
                '%s %s %s',
                trans('reports.impact.concerning_prefix', ['count' => $count, 'category' => $categoryLabel]),
                trans('reports.impact.needs_attention'),
                trans('reports.impact.skill_suffix', ['skill' => $skill])
            ),

            ObservationSentiment::Neutral->value => trans('reports.impact.neutral_prefix', [
                'count' => $count,
                'category' => $categoryLabel,
            ]),

            default => null,
        };
    }

    /**
     * Get classification label from average.
     */
    protected function getClassificationFromAverage(?float $average): string
    {
        if ($average === null || $average === 0.0) {
            return 'N/A';
        }

        if ($average < 6.0) {
            return trans('reports.classifications.basic');
        }

        if ($average < 8.0) {
            return trans('reports.classifications.intermediate');
        }

        return trans('reports.classifications.advanced');
    }

    /**
     * Render student report as HTML.
     */
    public function renderStudentReportHtml(Student $student, ?int $periodId = null): string
    {
        $data = $this->generateStudentReport($student, $periodId);

        return view('reports.student-comprehensive', $data)->render();
    }
}
