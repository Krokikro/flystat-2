<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\LeadComment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('comment')->label('Add comment')->schema([Textarea::make('body')->required()->rows(3)])
                ->action(fn (array $data) => LeadComment::create(['lead_id' => $this->getRecord()->id, 'user_id' => auth()->id(), 'body' => $data['body']])),
            EditAction::make(),
        ];
    }
}
