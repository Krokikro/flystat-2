<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\RevalidateService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Http;

/**
 * All runtime configuration lives here so integrations can be switched on from the admin,
 * without redeploys: site mode, company, contacts, Telegram, JSM, mail, analytics.
 */
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 70;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    /** key => [group, secret] */
    public static function keys(): array
    {
        return [
            'site_mode' => ['site', false], 'preview_token' => ['site', true], 'account_url' => ['site', false], 'app_status' => ['site', false],
            'app_store_url' => ['site', false], 'play_store_url' => ['site', false], 'default_units' => ['site', false], 'certification_status' => ['site', false],
            'legal_version' => ['site', false],
            'company' => ['company', false], 'contact_emails' => ['company', false], 'social' => ['company', false], 'telegram_channels' => ['company', false],
            'telegram_bot_token' => ['telegram', true], 'telegram_leads_chat_id' => ['telegram', false], 'telegram_leads_topics' => ['telegram', false],
            'telegram_news_channels' => ['telegram', false], 'telegram_daily_digest' => ['telegram', false],
            'jsm_base_url' => ['jsm', false], 'jsm_email' => ['jsm', false], 'jsm_api_token' => ['jsm', true], 'jsm_service_desk_id' => ['jsm', false],
            'jsm_request_types' => ['jsm', false], 'jsm_webhook_secret' => ['jsm', true],
            'mail_enabled' => ['mail', false], 'mail_from' => ['mail', false],
            'analytics' => ['analytics', false],
        ];
    }

    public function mount(): void
    {
        $all = Setting::all_cached();
        $this->form->fill(array_intersect_key($all, self::keys()));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make('Settings')->tabs([
                Tab::make('Site')->schema([
                    Select::make('site_mode')->options(['demo' => 'Demo — closed from indexing, password on the front', 'published' => 'Published — indexable'])->default('demo')->required()
                        ->helperText('Demo: robots noindex + X-Robots-Tag on every page. Switching to Published also tells the front to drop its cache.'),
                    TextInput::make('preview_token')->helperText('Appended as ?preview=… to see draft pages on the front'),
                    TextInput::make('account_url')->label('Personal account URL')->url()->helperText('Button "Personal account" in header/footer and on /manual. Empty = link to /manual#account'),
                    Select::make('app_status')->options(['soon' => 'Coming soon', 'live' => 'Available in stores'])->default('soon'),
                    TextInput::make('app_store_url')->url(), TextInput::make('play_store_url')->url(),
                    Select::make('default_units')->options(['mmol' => 'mmol/L', 'mgdl' => 'mg/dL'])->default('mmol'),
                    TextInput::make('certification_status')->helperText('Short status line shown next to specs, e.g. "CE marking in progress — expected end of 2027"'),
                    TextInput::make('legal_version')->helperText('Version of consent texts stored with each lead, e.g. 2026-10'),
                ])->columns(2),
                Tab::make('Company & contacts')->schema([
                    KeyValue::make('company')->label('Legal entity')->keyLabel('Field')->valueLabel('Value')->helperText('name, name_cn, address, reg_number, founded, email, phone'),
                    KeyValue::make('contact_emails')->label('Emails by topic')->keyLabel('Topic')->valueLabel('Email'),
                    KeyValue::make('social')->label('Social links')->keyLabel('Network')->valueLabel('URL'),
                    KeyValue::make('telegram_channels')->label('Public Telegram channels by language')->keyLabel('Locale')->valueLabel('URL'),
                ])->columns(2),
                Tab::make('Telegram bot')->schema([
                    TextInput::make('telegram_bot_token')->password()->revealable()->helperText('From @BotFather. Leave empty to disable notifications.'),
                    TextInput::make('telegram_leads_chat_id')->label('Leads group chat id')->helperText('Negative id of the supergroup with topics, e.g. -1001234567890'),
                    KeyValue::make('telegram_leads_topics')->label('Topic (thread) id per cohort')->keyLabel('Cohort')->valueLabel('Thread id'),
                    KeyValue::make('telegram_news_channels')->label('Autopost channels per language')->keyLabel('Locale')->valueLabel('@channel or chat id'),
                    Toggle::make('telegram_daily_digest')->label('Send daily digest at 09:00 (new / overdue leads)'),
                ])->columns(2),
                Tab::make('Jira Service Management')->schema([
                    TextInput::make('jsm_base_url')->url()->placeholder('https://mlc-heatlh.atlassian.net'),
                    TextInput::make('jsm_email')->email()->label('Service account email'),
                    TextInput::make('jsm_api_token')->password()->revealable(),
                    TextInput::make('jsm_service_desk_id')->label('Service desk id'),
                    KeyValue::make('jsm_request_types')->label('Request type id per kind')->keyLabel('Kind (bug / question / idea / other)')->valueLabel('requestTypeId'),
                    TextInput::make('jsm_webhook_secret')->password()->revealable()->helperText('Send it as X-Webhook-Secret header from the JSM automation → POST /api/v2/webhooks/jsm'),
                ])->columns(2),
                Tab::make('Mail')->schema([
                    Toggle::make('mail_enabled')->label('Send auto-replies to applicants')->helperText('SMTP credentials come from env (MAIL_*). Enable once the sysadmin connects the mail domain.'),
                    TextInput::make('mail_from')->email()->label('From address'),
                ])->columns(2),
                Tab::make('Analytics')->schema([
                    KeyValue::make('analytics')->label('Counters')->keyLabel('Provider (ga4 / ym / plausible / gtm)')->valueLabel('ID')->helperText('Loaded on the front only after cookie consent for analytics.'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach (self::keys() as $key => [$group, $secret]) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key], $group, $secret);
            }
        }
        RevalidateService::paths(['*']);
        Notification::make()->title('Settings saved')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testTelegram')->label('Test Telegram')->icon(Heroicon::PaperAirplane)->action(function () {
                $ok = app(\App\Services\TelegramService::class)->send(Setting::get('telegram_leads_chat_id'), '✅ Flystat admin: Telegram connected');
                Notification::make()->title($ok ? 'Message sent' : 'Failed — check token and chat id')->status($ok ? 'success' : 'danger')->send();
            }),
            Action::make('testJsm')->label('Test JSM')->icon(Heroicon::Ticket)->action(function () {
                try {
                    $r = Http::withBasicAuth(Setting::get('jsm_email'), Setting::get('jsm_api_token'))->get(rtrim(Setting::get('jsm_base_url'), '/').'/rest/servicedeskapi/servicedesk/'.Setting::get('jsm_service_desk_id'));
                    Notification::make()->title($r->successful() ? 'JSM reachable: '.($r->json('projectName') ?? 'ok') : 'JSM error '.$r->status())->status($r->successful() ? 'success' : 'danger')->send();
                } catch (\Throwable $e) {
                    Notification::make()->title('JSM error: '.$e->getMessage())->danger()->send();
                }
            }),
            Action::make('save')->label('Save')->action('save')->keyBindings(['mod+s']),
        ];
    }
}
