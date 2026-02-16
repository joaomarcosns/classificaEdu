<?php

namespace App\Console\Commands;

use App\Services\ClassificationService;
use Illuminate\Console\Command;

class RecalculateClassifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:recalculate-classifications {--period= : Specific evaluation period to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate student classifications based on current grades';

    /**
     * Execute the console command.
     */
    public function handle(ClassificationService $classificationService): int
    {
        $period = $this->option('period');

        $this->info('Recalculating student classifications...');

        $count = $classificationService->recalculateAllClassifications($period);

        $this->info("Successfully recalculated classifications for {$count} students.");

        return Command::SUCCESS;
    }
}
