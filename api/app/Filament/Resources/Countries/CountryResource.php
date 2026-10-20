<?php

namespace App\Filament\Resources\Countries;

use App\Filament\Resources\Countries\Pages\CreateCountry;
use App\Filament\Resources\Countries\Pages\EditCountry;
use App\Filament\Resources\Countries\Pages\ListCountries;
use App\Filament\Support\RoleAccess;
use App\Filament\Support\Tr;
use App\Models\Country;
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


class CountryResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = Country::class;

    protected static array $roles = ['admin', 'editor', 'manager'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeEuropeAfrica;

    protected static string|\UnitEnum|null $navigationGroup = 'Dictionaries';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('code')->label('ISO-2 code')->required()->maxLength(3),
                Select::make('region')->options(['middle_east' => 'Middle East', 'europe' => 'Europe', 'africa' => 'Africa', 'asia' => 'Asia', 'americas' => 'Americas', 'cis' => 'CIS'])->required(),
                Select::make('status')->options(['present' => 'Distributor present', 'negotiation' => 'In negotiation', 'free' => 'Open region'])->default('free')->required(),
                Toggle::make('is_priority')->label('Priority market'),
            ])->columns(4),
            Tr::text('name', 'Country name', true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->sortable()->searchable(),
            TextColumn::make('name')->label('Name')->formatStateUsing(fn ($record) => $record->getTranslation('name', 'en'))->searchable()->wrap(),
            TextColumn::make('region')->badge()->sortable(),
            TextColumn::make('status')->badge()->color(fn ($state) => match ($state) { 'present' => 'success', 'negotiation' => 'warning', default => 'gray' }),
            IconColumn::make('is_priority')->boolean(),
        ])->defaultSort('code')->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCountries::route('/'), 'create' => CreateCountry::route('/create'), 'edit' => EditCountry::route('/{record}/edit')];
    }
}
