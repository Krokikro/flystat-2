<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\HandlesTranslations;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HandlesTranslations { mutateFormDataBeforeFill as fillTranslations; }
    use SyncsBlocks;

    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->fillTranslations($data);
        $data['blocks'] = $this->blocksToForm($this->getRecord());

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractBlocks($data);
    }

    protected function afterSave(): void
    {
        $this->persistBlocks($this->getRecord());
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
