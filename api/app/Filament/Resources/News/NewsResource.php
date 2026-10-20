<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Jobs\PostNewsToTelegram;
use App\Models\News;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = News::class;

    protected static array $roles = ['admin', 'editor'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('News')->tabs([
                Tab::make('Content')->schema([
                    Section::make()->schema([
                        Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required(),
                        DateTimePicker::make('published_at')->label('Publish at')->helperText('Leave in the future for scheduled publishing'),
                        Select::make('category')->options(['news' => 'News', 'events' => 'Events', 'research' => 'Research', 'company' => 'Company'])->default('news'),
                        TextInput::make('author'),
                        FileUpload::make('cover')->image()->disk('public')->directory('news')->imageEditor(),
                        TagsInput::make('tags'),
                    ])->columns(3),
                    Tr::text('title', 'Title', true),
                    Tr::text('slug', 'Slug (per language)', true),
                    Tr::textarea('excerpt', 'Lead / excerpt', 3),
                    Tr::rich('body', 'Body'),
                ]),
                Tab::make('SEO')->schema([Tr::text('seo.title', 'Meta title'), Tr::textarea('seo.description', 'Meta description', 2), FileUpload::make('seo.og_image')->image()->disk('public')->directory('og')]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('cover')->disk('public')->square(),
            TextColumn::make('title')->formatStateUsing(fn ($record) => $record->getTranslation('title', 'en'))->searchable()->wrap(),
            TextColumn::make('category')->badge(),
            TextColumn::make('status')->badge()->color(fn ($state) => $state === 'published' ? 'success' : 'gray'),
            TextColumn::make('published_at')->date()->sortable(),
            TextColumn::make('telegram_posted_at')->label('Telegram')->since()->placeholder('—'),
        ])->defaultSort('published_at', 'desc')->recordActions([
            EditAction::make(),
            Action::make('telegram')->label('Post to Telegram')->icon(Heroicon::PaperAirplane)->requiresConfirmation()
                ->visible(fn (News $r) => $r->status === 'published' && ! $r->telegram_posted_at)->action(fn (News $r) => PostNewsToTelegram::dispatch($r->id)),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListNews::route('/'), 'create' => CreateNews::route('/create'), 'edit' => EditNews::route('/{record}/edit')];
    }
}
