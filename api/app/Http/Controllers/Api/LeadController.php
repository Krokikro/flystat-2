<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CreateJsmTicket;
use App\Jobs\NotifyLeadCreated;
use App\Jobs\SendLeadAutoReply;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function store(Request $r): JsonResponse
    {
        // Honeypot + rate limit (per IP, 10/hour)
        if (filled($r->input('website_url'))) {
            return response()->json(['ok' => true, 'id' => 0]);
        }
        $key = 'leads:'.$r->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['message' => 'Too many requests'], 429);
        }
        RateLimiter::hit($key, 3600);

        $data = $r->validate([
            'cohort' => ['required', Rule::in(array_keys(config('flystat.cohorts')))],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:3'],
            'message' => ['nullable', 'string', 'max:5000'],
            'fields' => ['nullable', 'array'],
            'locale' => ['nullable', 'string', 'max:5'],
            'source_page' => ['nullable', 'string', 'max:300'],
            'utm' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
            'consent' => ['accepted'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,heic,mp4,mov'],
        ]);

        $attachments = [];
        foreach ($r->file('attachments', []) as $file) {
            $attachments[] = $file->store('leads/'.date('Y/m'), 'public');
        }

        $lead = Lead::create($data + [
            'attachments' => $attachments,
            'consent_version' => Setting::get('legal_version'),
            'meta' => ($data['meta'] ?? []) + ['ip' => $r->ip(), 'ua' => mb_strimwidth((string) $r->userAgent(), 0, 250), 'referrer' => $r->headers->get('referer')],
        ]);

        NotifyLeadCreated::dispatch($lead->id);
        SendLeadAutoReply::dispatch($lead->id);
        if ($lead->cohort === 'support') {
            CreateJsmTicket::dispatch($lead->id);
        }

        return response()->json(['ok' => true, 'id' => $lead->id], 201);
    }
}
