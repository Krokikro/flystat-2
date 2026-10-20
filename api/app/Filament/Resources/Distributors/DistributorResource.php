<?php

namespace App\Filament\Resources\Distributors;

use App\Filament\Resources\Distributors\Pages\CreateDistributor;
use App\Filament\Resources\Distributors\Pages\EditDistributor;
use App\Filament\Resources\Distributors\Pages\ListDistributors;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Models\Distributor;
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


class DistributorResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = Distributor::class;

    protected static array $roles = ['admin', 'manager'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'Dictionaries';

    protected static ?int $navigationSort = 61;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->required(),
                TagsInput::make('countries')->label('Country ISO-2 codes')->placeholder('AE'),
                TextInput::make('contact_name'), TextInput::make('contact_email')->email(), TextInput::make('contact_phone'), TextInput::make('website')->url(),
                FileUpload::make('logo')->image()->disk('public')->directory('distributors'),
                Toggle::make('is_published')->label('Show on the website map'),
                Textarea::make('notes')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(), TextColumn::make('countries')->badge(), TextColumn::make('contact_email'), IconColumn::make('is_published')->boolean(),
        ])->defaultSort('name')->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDistributors::route('/'), 'create' => CreateDistributor::route('/create'), 'edit' => EditDistributor::route('/{record}/edit')];
    }
}
