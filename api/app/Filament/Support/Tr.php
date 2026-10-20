<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Builds a locale-tabbed group of fields for a translatable attribute.
 * State path becomes "{name}.{locale}" so it maps straight onto spatie/translatable arrays.
 */
class Tr
{
    public static function locales(): array
    {
        return config('flystat.locales');
    }

    public static function text(string $name, string $label, bool $required = false): Tabs
    {
        return self::tabs($name, $label, fn (string $l) => TextInput::make("$name.$l")->label(strtoupper($l))->required($required && $l === 'en')->maxLength(500));
    }

    public static function textarea(string $name, string $label, int $rows = 3): Tabs
    {
        return self::tabs($name, $label, fn (string $l) => Textarea::make("$name.$l")->label(strtoupper($l))->rows($rows));
    }

    public static function rich(string $name, string $label): Tabs
    {
        return self::tabs($name, $label, fn (string $l) => RichEditor::make("$name.$l")->label(strtoupper($l))
            ->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'table', 'undo', 'redo']));
    }

    public static function tabs(string $name, string $label, Closure $field): Tabs
    {
        return Tabs::make($label)->tabs(array_map(fn (string $l) => Tab::make(strtoupper($l))->schema([$field($l)]), self::locales()))
            ->contained(false)->columnSpanFull();
    }
}
