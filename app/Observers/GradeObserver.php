<?php

namespace App\Observers;

use App\Models\Grade;
use App\Services\ClassificationService;

class GradeObserver
{
    public function __construct(protected ClassificationService $classificationService)
    {
    }

    /**
     * Handle the Grade "created" event.
     */
    public function created(Grade $grade): void
    {
        $this->recalculateClassification($grade);
    }

    /**
     * Handle the Grade "updated" event.
     */
    public function updated(Grade $grade): void
    {
        $this->recalculateClassification($grade);
    }

    /**
     * Handle the Grade "deleted" event.
     */
    public function deleted(Grade $grade): void
    {
        $this->recalculateClassification($grade);
    }

    /**
     * Recalculate student classification.
     */
    protected function recalculateClassification(Grade $grade): void
    {
        if ($grade->student) {
            $this->classificationService->classifyStudent($grade->student);
        }
    }
}
