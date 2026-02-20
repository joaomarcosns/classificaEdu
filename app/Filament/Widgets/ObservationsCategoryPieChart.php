<?php

namespace App\Filament\Widgets;

use App\Enums\ObservationCategory;
use App\Models\Observation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ObservationsCategoryPieChart extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 6;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $observations = Observation::query()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $labels = $observations->map(function ($row): string {
            if ($row->category instanceof ObservationCategory) {
                return $row->category->label();
            }

            $category = is_string($row->category) ? $row->category : null;

            if ($category !== null) {
                return ObservationCategory::tryFrom($category)?->label() ?? $category;
            }

            return trans('observations.categories.other');
        })->all();

        $data = $observations->map(fn ($row) => (int) $row->total)->all();

        return [
            'datasets' => [
                [
                    'label' => trans('widgets.observations_category_pie.dataset_label'),
                    'data' => $data,
                    'backgroundColor' => [
                        '#60a5fa',
                        '#34d399',
                        '#f59e0b',
                        '#f87171',
                        '#a78bfa',
                        '#9ca3af',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): ?string
    {
        return trans('widgets.observations_category_pie.heading');
    }
}
