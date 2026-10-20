<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    use \App\Filament\Support\HandlesTranslations;

    protected static string $resource = FaqResource::class;

}
