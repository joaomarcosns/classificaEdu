<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Category to skill mapping for impact analysis.
     */
    protected const SKILL_MAPPING = [
        'comportamento' => 'conduta geral em sala de aula',
        'participacao' => 'engajamento nas atividades',
        'cooperacao' => 'trabalho em equipe e colaboração',
        'responsabilidade' => 'cumprimento de tarefas e compromissos',
        'interacao_social' => 'relacionamento interpessoal',
        'outro' => 'aspectos diversos do desenvolvimento',
    ];

    /**
     * Generate comprehensive student report data.
     */
    public function generateStudentReport(Student $student, ?string $period = null): array
    {
        $student->load(['grades', 'observations.user', 'classification']);

        return [
            'student' => $student,
            'classification' => $student->classification,
            'grades' => $this->getGradesData($student, $period),
            'observations' => $this->getObservationsData($student),
            'impact_analysis' => $this->generateImpactAnalysis($student),
            'generated_at' => now(),
            'period' => $period,
        ];
    }

    /**
     * Get grades data with period breakdown.
     */
    protected function getGradesData(Student $student, ?string $period): array
    {
        $query = $student->grades();

        if ($period) {
            $query->where('evaluation_period', $period);
        }

        $grades = $query->orderBy('evaluation_date')->get();

        $byPeriod = $grades->groupBy('evaluation_period')->map(function ($periodGrades) {
            return [
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

        $byCategory = $observations->groupBy('category');
        $bySentiment = $observations->groupBy('sentiment');

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

        $grouped = $observations->groupBy('category');

        foreach ($grouped as $category => $categoryObservations) {
            $sentiments = $categoryObservations->groupBy('sentiment');

            $insights = [];

            foreach ($sentiments as $sentiment => $sentimentObservations) {
                $count = $sentimentObservations->count();
                $skill = self::SKILL_MAPPING[$category] ?? 'desenvolvimento geral';

                $insight = $this->buildInsight($count, $category, $sentiment, $skill);

                if ($insight) {
                    $insights[] = $insight;
                }
            }

            if (! empty($insights)) {
                $analysis[$category] = [
                    'category_label' => trans("observations.categories.{$category}"),
                    'skill' => self::SKILL_MAPPING[$category] ?? 'desenvolvimento geral',
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
            'positivo' => "{$count} " . ($count === 1 ? 'observação positiva' : 'observações positivas')
                . " em {$categoryLabel} indicam impacto positivo em {$skill}",

            'preocupante' => "{$count} " . ($count === 1 ? 'observação preocupante' : 'observações preocupantes')
                . " em {$categoryLabel} requerem atenção e acompanhamento em {$skill}",

            'neutro' => "{$count} " . ($count === 1 ? 'observação neutra' : 'observações neutras')
                . " em {$categoryLabel} foram registradas",

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
            return 'Básico';
        }

        if ($average < 8.0) {
            return 'Intermediário';
        }

        return 'Avançado';
    }

    /**
     * Render student report as HTML.
     */
    public function renderStudentReportHtml(Student $student, ?string $period = null): string
    {
        $data = $this->generateStudentReport($student, $period);

        return view('reports.student-comprehensive', $data)->render();
    }
}
