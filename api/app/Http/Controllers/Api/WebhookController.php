<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /** JSM automation → POST {issue:{key, status}} with header X-Webhook-Secret (setting jsm_webhook_secret). */
    public function jsm(Request $r): JsonResponse
    {
        $secret = Setting::get('jsm_webhook_secret');
        if (! $secret || ! hash_equals($secret, (string) $r->header('X-Webhook-Secret', $r->query('secret', '')))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $key = data_get($r->all(), 'issue.key') ?? $r->input('key');
        $status = data_get($r->all(), 'issue.fields.status.name') ?? $r->input('status');
        $lead = Lead::where('ticket_key', $key)->first();
        if ($lead && $status) {
            $update = ['ticket_status' => $status];
            if (in_array(strtolower($status), ['done', 'resolved', 'closed', 'готово', 'закрыт'])) {
                $update['status'] = 'processed';
            }
            $lead->update($update);
        }

        return response()->json(['ok' => (bool) $lead]);
    }
}
