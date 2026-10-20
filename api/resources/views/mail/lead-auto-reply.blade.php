<x-mail::message>
# {{ __('mail.greeting', ['name' => $lead->name ? ', '.$lead->name : '']) }}

{{ __('mail.body_'.($lead->cohort === 'support' ? 'support' : ($lead->cohort === 'distribution' ? 'distribution' : 'default'))) }}

@if($lead->ticket_key)
**{{ __('mail.ticket') }}:** {{ $lead->ticket_key }}
@endif

{{ __('mail.signature') }}
</x-mail::message>
