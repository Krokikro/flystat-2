<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Console\Command;

/** Daily 09:00 digest + overdue reminders to the managers' Telegram group (PRD B4). */
class LeadsDigest extends Command
{
    protected $signature = 'leads:digest {--overdue : only overdue reminders}';

    protected $description = 'Send the daily leads digest / overdue reminders to Telegram';

    public function handle(TelegramService $tg): int
    {
        if (! $tg->isConfigured()) {
            $this->info('Telegram not configured — skipped');

            return self::SUCCESS;
        }
        $chat = Setting::get('telegram_leads_chat_id');
        $admin = rtrim(config('app.url'), '/').'/admin/leads';
        $overdue = Lead::where('status', 'new')->where('created_at', '<', now()->subHours(24))->get();
        if ($this->option('overdue')) {
            if ($overdue->isEmpty()) {
                return self::SUCCESS;
            }
            $lines = $overdue->take(15)->map(fn ($l) => sprintf('• #%d %s — %s (%s)', $l->id, e($l->cohortLabel()), e($l->name ?: $l->email), $l->created_at->diffForHumans()))->implode("\n");
            $tg->send($chat, "<b>⚠ Overdue leads (no action in 24 h): {$overdue->count()}</b>\n{$lines}", null, [['Open', $admin.'?tableFilters[overdue][isActive]=1']]);

            return self::SUCCESS;
        }
        if (! Setting::get('telegram_daily_digest')) {
            return self::SUCCESS;
        }
        $since = now()->subDay();
        $new = Lead::where('created_at', '>=', $since)->get()->groupBy('cohort')->map->count();
        $byCohort = $new->map(fn ($c, $k) => e(config("flystat.cohorts.$k", $k)).': '.$c)->implode("\n");
        $text = sprintf("<b>Flystat · daily digest %s</b>\nNew in 24 h: %d\n%s\nOpen (new): %d · overdue: %d · in progress: %d",
            now()->format('d.m'), $new->sum(), $byCohort ?: '—', Lead::where('status', 'new')->count(), $overdue->count(), Lead::where('status', 'in_progress')->count());
        $tg->send($chat, $text, null, [['Open CRM', $admin]]);

        return self::SUCCESS;
    }
}
