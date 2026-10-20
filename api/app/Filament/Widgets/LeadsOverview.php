<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $month = Lead::where('created_at', '>=', now()->startOfMonth());
        $prev = Lead::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $distribution = (clone $month)->where('cohort', 'distribution')->count();
        $processed = Lead::whereIn('status', ['processed'])->count();
        $total = Lead::whereNotIn('status', ['spam'])->count();
        $median = Lead::whereNotNull('first_action_at')->get()->map(fn ($l) => $l->created_at->diffInHours($l->first_action_at))->sort()->values();
        $med = $median->count() ? $median[intdiv($median->count(), 2)] : null;

        return [
            Stat::make('Leads this month', (clone $month)->count())->description(($prev ? 'vs '.$prev.' last month' : 'first month'))->color('primary'),
            Stat::make('Distribution (MoU)', $distribution)->description('target ≥ 15 / month')->color($distribution >= 15 ? 'success' : 'warning'),
            Stat::make('Processed', $total ? round($processed / $total * 100).'%' : '—')->description('target ≥ 70%'),
            Stat::make('Median first reaction', $med !== null ? round($med, 1).' h' : '—')->description('target ≤ 24 h')->color($med !== null && $med <= 24 ? 'success' : 'warning'),
            Stat::make('Overdue new leads', Lead::where('status', 'new')->where('created_at', '<', now()->subHours(24))->count())->color('danger'),
        ];
    }
}
