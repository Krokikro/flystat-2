<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsBySource extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Leads by country (90 days)';

    protected function getData(): array
    {
        $rows = Lead::where('created_at', '>=', now()->subDays(90))->whereNotNull('country')->selectRaw('country, count(*) as c')->groupBy('country')->orderByDesc('c')->limit(10)->get();

        return ['datasets' => [['label' => 'Leads', 'data' => $rows->pluck('c')->all(), 'backgroundColor' => '#2E90FA']], 'labels' => $rows->pluck('country')->all()];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
