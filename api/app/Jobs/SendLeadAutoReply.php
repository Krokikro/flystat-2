<?php

namespace App\Jobs;

use App\Mail\LeadAutoReply;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLeadAutoReply implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $leadId) {}

    public function handle(): void
    {
        $lead = Lead::find($this->leadId);
        if (! $lead || ! $lead->email || ! Setting::get('mail_enabled')) {
            return;
        }
        Mail::to($lead->email)->locale($lead->locale)->send(new LeadAutoReply($lead));
    }
}
