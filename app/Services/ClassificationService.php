<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentClassification;

class ClassificationService
{
    /**
     * Classification thresholds.
     */
    protected const BASICO_THRESHOLD = 6.0;
    protected const INTERMEDIARIO_THRESHOLD = 8.0;

    /**
     * Classify a student based on their grades.
     */
    public function classifyStudent(Student $student, ?string $period = null): StudentClassification
    {
        $average = $this->calculateOverallAverage($student, $period);
        $level = $this->determineClassificationLevel($average);
        $breakdown = $this->getHistoricalBreakdown($student);

        return StudentClassification::updateOrCreate(
            ['student_id' => $student->id],
            [
                'classification_level' => $level,
                'overall_average' => $average,
                'evaluation_period' => $period ?? 'current',
                'classification_date' => now(),
                'metadata' => $breakdown,
            ]
        );
    }

    /**
     * Calculate the overall average for a student.
     */
    public function calculateOverallAverage(Student $student, ?string $period = null): float
    {
        $query = $student->grades();

        if ($period) {
            $query->where('evaluation_period', $period);
        }

        $average = $query->avg('value');

        return $average ? round((float) $average, 2) : 0.0;
    }

    /**
     * Get historical breakdown of grades by period.
     */
    public function getHistoricalBreakdown(Student $student): array
    {
        $breakdown = [];
        $periods = ['trimestre_1', 'trimestre_2', 'trimestre_3', 'final'];

        foreach ($periods as $period) {
            $average = $student->grades()
                ->where('evaluation_period', $period)
                ->avg('value');

            if ($average !== null) {
                $breakdown[$period] = round((float) $average, 2);
            }
        }

        return $breakdown;
    }

    /**
     * Recalculate classifications for all students.
     */
    public function recalculateAllClassifications(?string $period = null): int
    {
        $students = Student::with('grades')->get();
        $count = 0;

        foreach ($students as $student) {
            if ($student->grades->isNotEmpty()) {
                $this->classifyStudent($student, $period);
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
        if ($average < self::BASICO_THRESHOLD) {
            return 'basico';
        }

        if ($average < self::INTERMEDIARIO_THRESHOLD) {
            return 'intermediario';
        }

        return 'avancado';
    }
}
