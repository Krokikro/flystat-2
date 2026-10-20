<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Support\RoleAccess;
use App\Jobs\CreateJsmTicket;
use App\Models\Lead;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadResource extends Resource
{
    use RoleAccess;

    protected static ?string $model = Lead::class;

    protected static array $roles = ['admin', 'manager'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Lead::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Lead::where('status', 'new')->where('created_at', '<', now()->subHours(24))->exists() ? 'danger' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                Section::make('Lead')->schema([
                    Select::make('cohort')->options(config('flystat.cohorts'))->required()->disabled(),
                    Select::make('status')->options(config('flystat.lead_statuses'))->required()->native(false),
                    Select::make('assigned_to')->label('Manager')->options(User::role(['manager', 'admin'])->pluck('name', 'id'))->searchable()->nullable(),
                    DateTimePicker::make('due_at')->label('Due'),
                    TextInput::make('name'), TextInput::make('email')->email(), TextInput::make('phone'), TextInput::make('company'),
                    TextInput::make('country')->maxLength(3),
                    Textarea::make('message')->rows(5)->columnSpanFull(),
                    KeyValue::make('fields')->label('Form fields')->columnSpanFull(),
                    Textarea::make('notes')->label('Internal notes')->rows(4)->columnSpanFull(),
                ])->columns(2)->columnSpan(2),
                Section::make('Source')->schema([
                    TextInput::make('locale')->disabled(), TextInput::make('source_page')->disabled(),
                    KeyValue::make('utm')->disabled(), KeyValue::make('meta')->disabled(),
                    TextInput::make('ticket_key')->label('JSM ticket')->disabled()->suffixAction(
                        Action::make('open')->icon(Heroicon::ArrowTopRightOnSquare)->url(fn (?Lead $r) => $r?->ticket_url)->openUrlInNewTab()->visible(fn (?Lead $r) => filled($r?->ticket_url))
                    ),
                    TextInput::make('ticket_status')->disabled(),
                ])->columnSpan(1),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('created_at')->label('Received')->since()->sortable()->description(fn (Lead $r) => $r->isOverdue() ? '⚠ overdue' : null),
            TextColumn::make('cohort')->badge()->formatStateUsing(fn ($state) => config("flystat.cohorts.$state", $state))
                ->color(fn ($state) => match ($state) { 'distribution' => 'success', 'support' => 'warning', 'waitlist' => 'info', default => 'gray' }),
            TextColumn::make('name')->searchable()->description(fn (Lead $r) => $r->company),
            TextColumn::make('email')->searchable()->copyable(),
            TextColumn::make('country')->sortable(),
            TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => config("flystat.lead_statuses.$state", $state))
                ->color(fn ($state) => match ($state) { 'new' => 'danger', 'in_progress' => 'warning', 'processed' => 'success', 'spam', 'declined' => 'gray', default => 'info' }),
            TextColumn::make('assignee.name')->label('Manager')->placeholder('—'),
            TextColumn::make('ticket_key')->label('JSM')->placeholder('—')->url(fn (Lead $r) => $r->ticket_url)->openUrlInNewTab(),
            TextColumn::make('locale')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('source_page')->toggleable(isToggledHiddenByDefault: true)->limit(30),
        ])->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('cohort')->options(config('flystat.cohorts'))->multiple(),
                SelectFilter::make('status')->options(config('flystat.lead_statuses'))->multiple(),
                SelectFilter::make('assigned_to')->label('Manager')->relationship('assignee', 'name'),
                SelectFilter::make('locale')->options(array_combine(config('flystat.locales'), config('flystat.locales'))),
                Filter::make('overdue')->label('Overdue (new > 24h)')->query(fn (Builder $q) => $q->where('status', 'new')->where('created_at', '<', now()->subHours(24))),
                Filter::make('mine')->label('Assigned to me')->query(fn (Builder $q) => $q->where('assigned_to', auth()->id())),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('take')->label('Take')->icon(Heroicon::HandRaised)->visible(fn (Lead $r) => ! $r->assigned_to)
                    ->action(fn (Lead $r) => $r->update(['assigned_to' => auth()->id(), 'status' => $r->status === 'new' ? 'in_progress' : $r->status])),
                Action::make('ticket')->label('Create JSM ticket')->icon(Heroicon::Ticket)->visible(fn (Lead $r) => $r->cohort === 'support' && ! $r->ticket_key)
                    ->action(fn (Lead $r) => CreateJsmTicket::dispatchSync($r->id)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign')->label('Assign to…')->schema([Select::make('user')->options(User::role(['manager', 'admin'])->pluck('name', 'id'))->required()])
                        ->action(fn (Collection $records, array $data) => $records->each->update(['assigned_to' => $data['user']])),
                    BulkAction::make('status')->label('Set status…')->schema([Select::make('status')->options(config('flystat.lead_statuses'))->required()])
                        ->action(fn (Collection $records, array $data) => $records->each->update(['status' => $data['status']])),
                    BulkAction::make('csv')->label('Export CSV')->icon(Heroicon::ArrowDownTray)->action(function (Collection $records) {
                        $cols = ['id', 'created_at', 'cohort', 'status', 'name', 'email', 'phone', 'company', 'country', 'locale', 'source_page', 'message', 'ticket_key'];
                        $out = fopen('php://temp', 'r+'); fputcsv($out, $cols);
                        foreach ($records as $r) {
                            fputcsv($out, array_map(fn ($c) => (string) $r->$c, $cols));
                        }
                        rewind($out);

                        return response()->streamDownload(fn () => print(stream_get_contents($out)), 'leads-'.date('Ymd-Hi').'.csv', ['Content-Type' => 'text/csv']);
                    }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListLeads::route('/'), 'view' => ViewLead::route('/{record}'), 'edit' => EditLead::route('/{record}/edit')];
    }
}
