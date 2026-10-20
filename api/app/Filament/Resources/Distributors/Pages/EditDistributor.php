<?php

namespace App\Filament\Resources\Distributors\Pages;

use App\Filament\Resources\Distributors\DistributorResource;
use Filament\Resources\Pages\EditRecord;

class EditDistributor extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = DistributorResource::class;

}
