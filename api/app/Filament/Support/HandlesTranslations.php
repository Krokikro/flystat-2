<?php

namespace App\Filament\Support;

/** Expose spatie translations as arrays in the form state (fill) — saving already accepts arrays. */
trait HandlesTranslations
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        if (! method_exists($record, 'getTranslatableAttributes')) {
            return $data;
        }
        foreach ($record->getTranslatableAttributes() as $attr) {
            $data[$attr] = $record->getTranslations($attr);
        }

        return $data;
    }
}
