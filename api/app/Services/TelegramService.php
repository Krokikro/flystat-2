<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * One bot for everything. Configured entirely from the admin (Settings → Integrations):
 * telegram_bot_token, telegram_leads_chat_id, telegram_leads_topics (json cohort => thread id),
 * telegram_news_channels (json locale => @channel). Silent no-op until configured.
 */
class TelegramService
{
    public function isConfigured(): bool
    {
        return filled(Setting::get('telegram_bot_token'));
    }

    public function send(string|int $chatId, string $text, ?int $threadId = null, array $buttons = []): bool
    {
        if (! $this->isConfigured() || ! $chatId) {
            return false;
        }
        $payload = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true];
        if ($threadId) {
            $payload['message_thread_id'] = $threadId;
        }
        if ($buttons) {
            $payload['reply_markup'] = ['inline_keyboard' => [array_map(fn ($b) => ['text' => $b[0], 'url' => $b[1]], $buttons)]];
        }
        try {
            $r = Http::timeout(10)->post('https://api.telegram.org/bot'.Setting::get('telegram_bot_token').'/sendMessage', $payload);
            if (! $r->successful()) {
                Log::warning('telegram.send_failed', ['status' => $r->status(), 'body' => $r->body()]);
            }

            return $r->successful();
        } catch (\Throwable $e) {
            Log::warning('telegram.exception', ['e' => $e->getMessage()]);

            return false;
        }
    }

    public function sendPhoto(string|int $chatId, string $photoUrl, string $caption): bool
    {
        if (! $this->isConfigured() || ! $chatId) {
            return false;
        }
        try {
            return Http::timeout(15)->post('https://api.telegram.org/bot'.Setting::get('telegram_bot_token').'/sendPhoto', [
                'chat_id' => $chatId, 'photo' => $photoUrl, 'caption' => $caption, 'parse_mode' => 'HTML',
            ])->successful();
        } catch (\Throwable $e) {
            Log::warning('telegram.exception', ['e' => $e->getMessage()]);

            return false;
        }
    }
}
