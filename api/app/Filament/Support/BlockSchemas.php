<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

/**
 * The block library. Every page is an ordered list of these. Text fields are locale maps (see Tr).
 * The front renders each type with components/blocks/<Type>.vue.
 */
class BlockSchemas
{
    public static function builder(string $name = 'blocks'): Builder
    {
        return Builder::make($name)->label('Blocks')->blocks(self::blocks())->collapsible()->collapsed()->blockNumbers(false)
            ->addActionLabel('Add block')->reorderableWithButtons()->columnSpanFull()
            ->blockPickerColumns(3);
    }

    protected static function common(): array
    {
        return [
            TextInput::make('anchor')->label('Anchor (id)')->maxLength(60)->helperText('Used for in-page links, e.g. "install" → /page#install'),
            Toggle::make('enabled')->default(true),
        ];
    }

    protected static function cta(string $prefix = 'cta'): array
    {
        return [
            Tr::text("$prefix.label", 'Button label'),
            TextInput::make("$prefix.href")->label('Button link')->helperText('Internal (/partners), anchor (#form) or https://'),
        ];
    }

    protected static function image(string $name = 'image', string $label = 'Image'): FileUpload
    {
        return FileUpload::make($name)->label($label)->image()->disk('public')->directory('blocks')->imageEditor()->maxSize(8192);
    }

    protected static function items(array $extra = []): Repeater
    {
        return Repeater::make('items')->schema(array_merge([
            Tr::text('title', 'Title'),
            Tr::textarea('text', 'Text', 3),
            TextInput::make('icon')->label('Icon name')->helperText('lucide icon id, e.g. "activity"'),
            self::image(),
        ], $extra))->collapsible()->collapsed()->itemLabel(fn (array $state) => $state['title']['en'] ?? null)->columnSpanFull();
    }

    public static function blocks(): array
    {
        return [
            Block::make('hero_dark')->label('Hero — dark 3D scene')->schema([
                Tr::text('eyebrow', 'Eyebrow (small line above title)'),
                Tr::text('title', 'Title', true),
                Tr::textarea('text', 'Lead paragraph'),
                Grid::make(2)->schema(array_merge(self::cta('cta'), self::cta('cta2'))),
                Select::make('scene')->options(['rotate' => 'Rotating device (scroll-driven)', 'exploded' => 'Exploded view', 'sensor' => 'Sensor close-up'])->default('rotate'),
                ...self::common(),
            ]),
            Block::make('hero_light')->label('Hero — light (partners / company)')->schema([
                Tr::text('eyebrow', 'Eyebrow'),
                Tr::text('title', 'Title', true),
                Tr::textarea('text', 'Lead paragraph'),
                Grid::make(2)->schema(array_merge(self::cta('cta'), self::cta('cta2'))),
                self::image(),
                Repeater::make('stats')->schema([TextInput::make('value')->label('Value'), Tr::text('label', 'Label')])->columns(1)->maxItems(4),
                ...self::common(),
            ]),
            Block::make('stats')->label('Key numbers')->schema([
                Tr::text('title', 'Title'),
                Repeater::make('items')->schema([TextInput::make('value')->required(), TextInput::make('suffix'), Tr::text('label', 'Label'), Tr::textarea('text', 'Footnote', 2)])->maxItems(6),
                ...self::common(),
            ]),
            Block::make('how_it_works')->label('How it works (steps with illustrations)')->schema([
                Tr::text('eyebrow', 'Eyebrow'), Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'),
                self::items(), ...self::common(),
            ]),
            Block::make('text_media')->label('Text + media')->schema([
                Tr::text('eyebrow', 'Eyebrow'), Tr::text('title', 'Title'), Tr::rich('text', 'Text'),
                self::image(), TextInput::make('video')->label('Video URL (mp4)'),
                Select::make('media_side')->options(['right' => 'Media right', 'left' => 'Media left'])->default('right'),
                Select::make('tone')->options(['light' => 'Light', 'warm' => 'Warm', 'dark' => 'Dark'])->default('light'),
                Grid::make(2)->schema(self::cta()), Toggle::make('generated_badge')->label('Show "visualisation" badge'),
                ...self::common(),
            ]),
            Block::make('cards')->label('Card grid')->schema([
                Tr::text('eyebrow', 'Eyebrow'), Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'),
                Select::make('columns')->options([2 => '2', 3 => '3', 4 => '4'])->default(3),
                self::items([TextInput::make('href')->label('Link')]), ...self::common(),
            ]),
            Block::make('steps')->label('Numbered steps (installation)')->schema([
                Tr::text('eyebrow', 'Eyebrow'), Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'),
                self::items(), Grid::make(2)->schema(self::cta()), ...self::common(),
            ]),
            Block::make('spec_table')->label('Specification table')->schema([
                Tr::text('title', 'Title'),
                Repeater::make('rows')->schema([Tr::text('label', 'Parameter'), Tr::text('value', 'Value'), Tr::text('note', 'Note')])->columnSpanFull(),
                Tr::textarea('footnote', 'Footnote (certification status etc.)', 2),
                ...self::common(),
            ]),
            Block::make('comparison')->label('Comparison with the market')->schema([
                Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'),
                Repeater::make('rows')->schema([Tr::text('label', 'Criterion'), Tr::text('flystat', 'Flystat'), Tr::text('market', 'Typical market')])->columnSpanFull(),
                Tr::textarea('footnote', 'Footnote', 2), ...self::common(),
            ]),
            Block::make('faq')->label('FAQ (from the FAQ library)')->schema([
                Tr::text('title', 'Title'),
                Select::make('categories')->multiple()->options(['device' => 'Device', 'app' => 'App', 'purchase' => 'Purchase', 'partners' => 'Partners', 'clinicians' => 'Clinicians', 'support' => 'Support'])->helperText('Empty = all'),
                TextInput::make('limit')->numeric()->default(6),
                Toggle::make('searchable')->label('Show search'),
                ...self::common(),
            ]),
            Block::make('form')->label('Lead form')->schema([
                Select::make('cohort')->options(config('flystat.cohorts'))->required(),
                Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'), Tr::text('button', 'Submit button label'),
                Tr::textarea('success', 'Success message', 2),
                Select::make('tone')->options(['light' => 'Light', 'dark' => 'Dark'])->default('light'),
                ...self::common(),
            ]),
            Block::make('news')->label('Latest news')->schema([
                Tr::text('title', 'Title'), TextInput::make('limit')->numeric()->default(3), ...self::common(),
            ]),
            Block::make('map')->label('Distributor map')->schema([
                Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'),
                Toggle::make('show_distributors')->label('Show distributor names'),
                Toggle::make('contact_form')->label('Show "contact a distributor" form'),
                ...self::common(),
            ]),
            Block::make('cta')->label('Call to action band')->schema([
                Tr::text('title', 'Title'), Tr::textarea('text', 'Text'), Grid::make(2)->schema(array_merge(self::cta(), self::cta('cta2'))),
                Select::make('tone')->options(['dark' => 'Dark', 'purple' => 'Purple (app)', 'light' => 'Light'])->default('dark'),
                ...self::common(),
            ]),
            Block::make('app_screens')->label('App screens showcase')->schema([
                Tr::text('eyebrow', 'Eyebrow'), Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'),
                Repeater::make('screens')->schema([Tr::text('title', 'Feature'), Tr::textarea('text', 'Description', 2), self::image('image', 'Screen')])->columnSpanFull(),
                ...self::common(),
            ]),
            Block::make('segments')->label('Who it is for (segments)')->schema([
                Tr::text('eyebrow', 'Eyebrow'), Tr::text('title', 'Title'),
                self::items([TextInput::make('href')->label('Link')]), ...self::common(),
            ]),
            Block::make('timeline')->label('Timeline')->schema([
                Tr::text('title', 'Title'),
                Repeater::make('items')->schema([TextInput::make('date')->label('Date / year'), Tr::text('title', 'Title'), Tr::textarea('text', 'Text', 2)])->columnSpanFull(),
                ...self::common(),
            ]),
            Block::make('manual_sections')->label('User manual (from Documents → manual)')->schema([
                Tr::text('title', 'Title'), Tr::textarea('text', 'Intro'), ...self::common(),
            ]),
            Block::make('account_link')->label('Personal account link')->schema([
                Tr::text('title', 'Title'), Tr::textarea('text', 'Text'), Tr::text('button', 'Button label'), ...self::common(),
            ]),
            Block::make('documents')->label('Downloadable documents')->schema([
                Tr::text('title', 'Title'),
                Select::make('types')->multiple()->options(['spec' => 'Specification', 'presskit' => 'Press kit', 'presentation' => 'Presentation', 'manual' => 'Manual'])->default(['spec', 'presskit']),
                ...self::common(),
            ]),
            Block::make('contacts')->label('Contacts')->schema([
                Tr::text('title', 'Title'),
                Repeater::make('items')->schema([Tr::text('label', 'Topic'), TextInput::make('email')->email(), Tr::textarea('text', 'Note', 2)])->columnSpanFull(),
                Toggle::make('show_company')->label('Show legal entity')->default(true),
                ...self::common(),
            ]),
            Block::make('team_quote')->label('Quote')->schema([
                Tr::textarea('text', 'Quote'), TextInput::make('author'), Tr::text('role', 'Role'), self::image('image', 'Portrait'), ...self::common(),
            ]),
            Block::make('video')->label('Video')->schema([
                Tr::text('title', 'Title'), TextInput::make('video')->label('Video URL (mp4)')->required(), self::image('poster', 'Poster'),
                Toggle::make('generated_badge')->label('Show "visualisation" badge'), Tr::textarea('caption', 'Caption', 2),
                ...self::common(),
            ]),
        ];
    }
}
