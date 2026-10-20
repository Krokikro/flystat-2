<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PostNewsToTelegram implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $newsId) {}

    public function handle(TelegramService $tg): void
    {
        $news = News::find($this->newsId);
        if (! $news || $news->telegram_posted_at || $news->status !== 'published' || ! $tg->isConfigured()) {
            return;
        }
        $channels = Setting::get('telegram_news_channels') ?? [];
        $web = rtrim(config('flystat.web_url'), '/');
        $sent = false;
        foreach ($channels as $locale => $channel) {
            $title = $news->getTranslation('title', $locale, false) ?: $news->getTranslation('title', 'en');
            $excerpt = $news->getTranslation('excerpt', $locale, false) ?: $news->getTranslation('excerpt', 'en');
            $slug = $news->getTranslation('slug', $locale, false) ?: $news->getTranslation('slug', 'en');
            $url = "$web/$locale/news/$slug";
            $caption = "<b>".e($title)."</b>\n\n".e($excerpt)."\n\n".$url;
            $ok = $news->cover ? $tg->sendPhoto($channel, $news->cover, $caption) : $tg->send($channel, $caption);
            $sent = $sent || $ok;
        }
        if ($sent) {
            $news->forceFill(['telegram_posted_at' => now()])->saveQuietly();
        }
    }
}
