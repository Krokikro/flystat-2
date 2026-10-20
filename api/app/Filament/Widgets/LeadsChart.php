<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Leads by week and cohort';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $weeks = collect(range(11, 0))->map(fn ($i) => now()->subWeeks($i)->startOfWeek());
        $datasets = [];
        $colors = ['distribution' => '#12B76A', 'waitlist' => '#2E90FA', 'support' => '#F79009', 'cooperation' => '#7A5AF8', 'distributor_contact' => '#6D81A2', 'subscribe' => '#ADBED6'];
        foreach (config('flystat.cohorts') as $cohort => $label) {
            $datasets[] = ['label' => $label, 'backgroundColor' => $colors[$cohort] ?? '#999',
                'data' => $weeks->map(fn ($w) => Lead::where('cohort', $cohort)->whereBetween('created_at', [$w, $w->copy()->endOfWeek()])->count())->all()];
        }

        return ['datasets' => $datasets, 'labels' => $weeks->map(fn ($w) => $w->format('d.m'))->all()];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return ['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]]];
    }
}
