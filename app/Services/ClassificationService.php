<?php

namespace App\Services;

use App\Enums\ClassificationLevel;
use App\Models\EvaluationPeriod;
use App\Models\Student;
use App\Models\StudentClassification;

class ClassificationService
{
    /**
     * Classification thresholds.
     */
    protected const BASIC_THRESHOLD = 6.0;

    protected const INTERMEDIATE_THRESHOLD = 8.0;

    /**
     * Classify a student based on their grades.
     */
    public function classifyStudent(Student $student, ?int $periodId = null): StudentClassification
    {
        $average = $this->calculateOverallAverage($student, $periodId);
        $level = $this->determineClassificationLevel($average);
        $breakdown = $this->getHistoricalBreakdown($student);

        return StudentClassification::updateOrCreate(
            ['student_id' => $student->id],
            [
                'classification_level' => $level,
                'overall_average' => $average,
                'evaluation_period' => $periodId ? (string) $periodId : 'current',
                'classification_date' => now(),
                'metadata' => $breakdown,
            ]
        );
    }

    /**
     * Calculate the overall average for a student.
     */
    public function calculateOverallAverage(Student $student, ?int $periodId = null): float
    {
        $query = $student->grades();

        if ($periodId) {
            $query->where('period_id', $periodId);
        }

        $average = $query->avg('value');

        return $average ? round((float) $average, 2) : 0.0;
    }

    /**
     * Get historical breakdown of grades by period.
     *
     * @return array<int, float>
     */
    public function getHistoricalBreakdown(Student $student): array
    {
        $breakdown = [];

        $periods = EvaluationPeriod::query()->where('is_active', true)->orderBy('academic_year')->orderBy('order')->get();

        foreach ($periods as $period) {
            $average = $student->grades()
                ->where('period_id', $period->id)
                ->avg('value');

            if ($average !== null) {
                $breakdown[$period->id] = round((float) $average, 2);
            }
        }

        return $breakdown;
    }

    /**
     * Recalculate classifications for all students.
     */
    public function recalculateAllClassifications(?int $periodId = null): int
    {
        $students = Student::with('grades')->get();
        $count = 0;

        foreach ($students as $student) {
            if ($student->grades->isNotEmpty()) {
                $this->classifyStudent($student, $periodId);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Determine classification level based on average.
     */
    protected function determineClassificationLevel(float $average): string
    {
        if ($average < self::BASIC_THRESHOLD) {
            return ClassificationLevel::Basic->value;
        }

        if ($average < self::INTERMEDIATE_THRESHOLD) {
            return ClassificationLevel::Intermediate->value;
        }

        return ClassificationLevel::Advanced->value;
    }
}
