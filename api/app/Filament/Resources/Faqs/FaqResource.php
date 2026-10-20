<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class FaqResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = Faq::class;

    protected static array $roles = ['admin', 'editor'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('category')->options(['device' => 'Device', 'app' => 'App', 'purchase' => 'Purchase', 'partners' => 'Partners', 'clinicians' => 'Clinicians', 'support' => 'Support'])->required(),
                Select::make('pages')->multiple()->options(['home' => 'Home', 'device' => 'Device', 'app' => 'App', 'partners' => 'Partners', 'clinicians' => 'Clinicians', 'support' => 'Support', 'for' => 'For whom'])->helperText('Empty = everywhere the category is shown'),
                Toggle::make('is_published')->default(true), TextInput::make('sort')->numeric()->default(0),
            ])->columns(4),
            Tr::text('question', 'Question', true),
            Tr::rich('answer', 'Answer'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question')->label('Question')->formatStateUsing(fn ($record) => $record->getTranslation('question', 'en'))->searchable()->wrap(),
            TextColumn::make('category')->badge(),
            IconColumn::make('is_published')->boolean(),
            TextColumn::make('sort')->sortable(),
        ])->defaultSort('sort')->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListFaqs::route('/'), 'create' => CreateFaq::route('/create'), 'edit' => EditFaq::route('/{record}/edit')];
    }
}
