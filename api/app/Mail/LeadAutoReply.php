<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->lead->cohort) {
            'support' => __('mail.support_subject', ['ticket' => $this->lead->ticket_key ?? '']),
            'distribution' => __('mail.distribution_subject'),
            default => __('mail.default_subject'),
        };

        return new Envelope(subject: trim($subject));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.lead-auto-reply');
    }
}
