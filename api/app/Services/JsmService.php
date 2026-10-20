<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Jira Service Management. Settings (admin → Integrations): jsm_base_url (https://x.atlassian.net),
 * jsm_email, jsm_api_token, jsm_service_desk_id, jsm_request_types (json kind => requestTypeId).
 */
class JsmService
{
    public function isConfigured(): bool
    {
        return filled(Setting::get('jsm_base_url')) && filled(Setting::get('jsm_api_token')) && filled(Setting::get('jsm_service_desk_id'));
    }

    protected function client()
    {
        return Http::withBasicAuth(Setting::get('jsm_email'), Setting::get('jsm_api_token'))
            ->baseUrl(rtrim(Setting::get('jsm_base_url'), '/'))
            ->acceptJson()->timeout(20);
    }

    public function createRequest(Lead $lead): array
    {
        $kind = $lead->fields['kind'] ?? 'other';
        $types = Setting::get('jsm_request_types') ?? [];
        $requestTypeId = $types[$kind] ?? ($types['other'] ?? null);
        if (! $requestTypeId) {
            throw new \RuntimeException('JSM request type is not configured for kind '.$kind);
        }
        $summary = sprintf('[%s] %s — %s', strtoupper($kind), $lead->fields['subject'] ?? 'Website request', $lead->name ?: $lead->email);
        $desc = collect([
            'Name: '.$lead->name, 'Email: '.$lead->email, 'Country: '.$lead->country, 'Role: '.($lead->fields['role'] ?? ''),
            'Language: '.$lead->locale, 'Page: '.$lead->source_page, '', $lead->message, '',
            'Browser: '.($lead->meta['ua'] ?? ''), 'Screen: '.($lead->meta['screen'] ?? ''), 'Lead #'.$lead->id,
        ])->implode("\n");

        $r = $this->client()->post('/rest/servicedeskapi/request', [
            'serviceDeskId' => Setting::get('jsm_service_desk_id'),
            'requestTypeId' => $requestTypeId,
            'raiseOnBehalfOf' => $lead->email,
            'requestFieldValues' => ['summary' => $summary, 'description' => $desc],
        ]);
        $r->throw();
        $j = $r->json();

        return ['key' => $j['issueKey'] ?? null, 'url' => $j['_links']['web'] ?? null, 'status' => $j['currentStatus']['status'] ?? 'Open'];
    }
}
