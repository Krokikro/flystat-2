<?php

namespace App\Filament\Resources\MediaItems;

use App\Filament\Resources\MediaItems\Pages\CreateMediaItem;
use App\Filament\Resources\MediaItems\Pages\EditMediaItem;
use App\Filament\Resources\MediaItems\Pages\ListMediaItems;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Models\MediaItem;
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


class MediaItemResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = MediaItem::class;

    protected static array $roles = ['admin', 'editor'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                FileUpload::make('path')->label('File')->disk('public')->directory('media')->required()->maxSize(51200)
                    ->acceptedFileTypes(['image/*', 'video/mp4', 'video/webm', 'application/pdf', 'model/gltf-binary', 'application/octet-stream']),
                Select::make('kind')->options(['image' => 'Image', 'video' => 'Video', 'document' => 'Document', 'model' => '3D model'])->default('image'),
                TextInput::make('folder')->helperText('e.g. renders, lifestyle, production, app'),
                TagsInput::make('tags'),
                Toggle::make('is_generated')->label('AI-generated (shows "visualisation" badge)'),
            ])->columns(2),
            Tr::text('alt', 'Alt text'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\ImageColumn::make('path')->disk('public')->square()->visibility('public'),
            TextColumn::make('path')->searchable()->copyable()->limit(40)->description(fn ($record) => $record->url()),
            TextColumn::make('kind')->badge(), TextColumn::make('folder')->badge()->color('gray'), IconColumn::make('is_generated')->boolean()->label('AI'),
        ])->defaultSort('id', 'desc')->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListMediaItems::route('/'), 'create' => CreateMediaItem::route('/create'), 'edit' => EditMediaItem::route('/{record}/edit')];
    }
}
