<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('All')];
        foreach (config('flystat.cohorts') as $k => $label) {
            $tabs[$k] = Tab::make($label)->modifyQueryUsing(fn (Builder $q) => $q->where('cohort', $k))->badge(Lead::where('cohort', $k)->where('status', 'new')->count() ?: null);
        }

        return $tabs;
    }
}
