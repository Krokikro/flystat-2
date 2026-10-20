<?php

namespace App\Filament\Resources\MediaItems\Pages;

use App\Filament\Resources\MediaItems\MediaItemResource;
use Filament\Resources\Pages\EditRecord;

class EditMediaItem extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = MediaItemResource::class;

}
