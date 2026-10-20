<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = DocumentResource::class;

}
