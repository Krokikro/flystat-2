<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Models\Document;
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


class DocumentResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = Document::class;

    protected static array $roles = ['admin', 'editor'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('type')->options(['legal' => 'Legal (privacy, cookies, terms…)', 'manual' => 'User manual', 'spec' => 'Specification', 'presskit' => 'Press kit', 'presentation' => 'Distributor presentation'])->required(),
                TextInput::make('slug')->required()->maxLength(60),
                TextInput::make('version')->maxLength(30),
                DatePicker::make('effective_at')->label('Effective from'),
                Toggle::make('is_published')->default(true), TextInput::make('sort')->numeric()->default(0),
            ])->columns(3),
            Tr::text('title', 'Title', true),
            Tr::rich('body', 'Body (HTML, for legal / manual pages)'),
            KeyValue::make('files')->label('PDF files per language')->keyLabel('Locale')->valueLabel('Public URL or storage path')->helperText('Upload PDFs in Media and paste their paths here, e.g. documents/manual-en.pdf'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Title')->formatStateUsing(fn ($record) => $record->getTranslation('title', 'en'))->searchable()->wrap(),
            TextColumn::make('type')->badge(), TextColumn::make('slug'), TextColumn::make('version'), IconColumn::make('is_published')->boolean(), TextColumn::make('effective_at')->date(),
        ])->defaultSort('sort')->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDocuments::route('/'), 'create' => CreateDocument::route('/create'), 'edit' => EditDocument::route('/{record}/edit')];
    }
}
