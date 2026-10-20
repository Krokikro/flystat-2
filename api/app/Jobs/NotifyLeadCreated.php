<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyLeadCreated implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function backoff(): array
    {
        return [30, 120, 600, 1800];
    }

    public function __construct(public int $leadId) {}

    public function handle(TelegramService $tg): void
    {
        $lead = Lead::find($this->leadId);
        if (! $lead || $lead->telegram_notified_at || ! $tg->isConfigured()) {
            return;
        }
        $chat = Setting::get('telegram_leads_chat_id');
        $topics = Setting::get('telegram_leads_topics') ?? [];
        $adminUrl = rtrim(config('app.url'), '/').'/admin/leads/'.$lead->id;
        $text = sprintf("<b>New lead · %s</b>\n%s\n%s%s%s\n%s\n<i>%s · %s</i>",
            e($lead->cohortLabel()), e($lead->name ?? ''), e($lead->email ?? ''),
            $lead->phone ? ' · '.e($lead->phone) : '', $lead->company ? ' · '.e($lead->company) : '',
            e(mb_strimwidth((string) $lead->message, 0, 300, '…')), e($lead->country ?? '—'), e($lead->source_page ?? ''));
        if ($tg->send($chat, $text, $topics[$lead->cohort] ?? null, [['Open in admin', $adminUrl]])) {
            $lead->forceFill(['telegram_notified_at' => now()])->saveQuietly();
        }
    }
}
