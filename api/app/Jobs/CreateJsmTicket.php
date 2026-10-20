<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\JsmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateJsmTicket implements ShouldQueue
{
    use Queueable;

    public int $tries = 8;

    public function backoff(): array
    {
        return [60, 300, 900, 3600, 7200];
    }

    public function __construct(public int $leadId) {}

    public function handle(JsmService $jsm): void
    {
        $lead = Lead::find($this->leadId);
        if (! $lead || $lead->ticket_key || ! $jsm->isConfigured()) {
            return;
        }
        $t = $jsm->createRequest($lead);
        $lead->forceFill(['ticket_key' => $t['key'], 'ticket_url' => $t['url'], 'ticket_status' => $t['status']])->saveQuietly();
    }
}
