<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = NewsResource::class;
}
