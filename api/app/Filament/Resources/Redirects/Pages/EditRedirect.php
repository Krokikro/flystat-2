<?php

namespace App\Filament\Resources\Redirects\Pages;

use App\Filament\Resources\Redirects\RedirectResource;
use Filament\Resources\Pages\EditRecord;

class EditRedirect extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = RedirectResource::class;

}
