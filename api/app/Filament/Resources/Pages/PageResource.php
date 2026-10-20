<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\BlockSchemas;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Models\Page;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = Page::class;

    protected static array $roles = ['admin', 'editor'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Page')->tabs([
                Tab::make('Content')->schema([
                    Section::make()->schema([
                        TextInput::make('slug')->required(fn ($record) => $record?->slug !== '')->helperText('Same for all languages. Empty = home page.')->maxLength(80),
                        Select::make('template')->options(array_combine($t = ['home', 'device', 'app', 'segment', 'clinicians', 'partners', 'company', 'news', 'support', 'manual', 'contacts', 'legal', 'service', 'default'], $t))->default('default')->required(),
                        Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required(),
                    ])->columns(3),
                    Tr::text('title', 'Page title (H1)', true),
                    BlockSchemas::builder('blocks'),
                ]),
                Tab::make('Navigation')->schema([
                    Tr::text('nav_label', 'Menu label'),
                    Toggle::make('in_header')->label('Show in header'),
                    Toggle::make('in_footer')->label('Show in footer'),
                    Select::make('menu_group')->options(['people' => 'For people', 'partners' => 'For partners'])->label('Header group'),
                    TextInput::make('sort')->numeric()->default(0),
                ])->columns(2),
                Tab::make('SEO')->schema([
                    Tr::text('seo.title', 'Meta title (≤ 60 chars)'),
                    Tr::textarea('seo.description', 'Meta description (≤ 160 chars)', 2),
                    FileUpload::make('seo.og_image')->image()->disk('public')->directory('og')->label('OG image (1200×630)'),
                    Toggle::make('seo.noindex')->label('noindex (even when site is published)'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Title')->formatStateUsing(fn ($record) => $record->getTranslation('title', 'en'))->searchable(),
            TextColumn::make('slug')->prefix('/')->sortable(),
            TextColumn::make('template')->badge(),
            TextColumn::make('status')->badge()->color(fn ($state) => $state === 'published' ? 'success' : 'gray'),
            TextColumn::make('blocks_count')->counts('blocks')->label('Blocks'),
            IconColumn::make('in_header')->boolean()->label('Header'),
            TextColumn::make('updated_at')->since()->sortable(),
        ])->defaultSort('sort')->reorderable('sort')
            ->recordActions([
                Action::make('preview')->icon(Heroicon::OutlinedEye)->url(fn (Page $r) => rtrim(config('flystat.web_url'), '/').$r->path('en').'?preview='.Setting::get('preview_token'))->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPages::route('/'), 'create' => CreatePage::route('/create'), 'edit' => EditPage::route('/{record}/edit')];
    }
}
