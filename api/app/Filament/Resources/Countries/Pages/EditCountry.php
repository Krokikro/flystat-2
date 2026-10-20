<?php

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Resources\Countries\CountryResource;
use Filament\Resources\Pages\EditRecord;

class EditCountry extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = CountryResource::class;

}
