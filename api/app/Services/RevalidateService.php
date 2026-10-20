<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Tells the Nuxt front to drop its ISR cache for given paths (or everything). */
class RevalidateService
{
    public static function paths(array $paths = ['*']): void
    {
        $secret = config('flystat.revalidate_secret');
        if (! $secret) {
            return;
        }
        try {
            Http::timeout(5)->post(rtrim(config('flystat.web_url'), '/').'/api/revalidate', ['secret' => $secret, 'paths' => $paths]);
        } catch (\Throwable $e) {
            Log::info('revalidate.failed', ['e' => $e->getMessage()]);
        }
    }
}
