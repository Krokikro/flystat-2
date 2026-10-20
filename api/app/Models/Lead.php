<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lead extends Model
{
    use LogsActivity;

    protected $fillable = ['cohort', 'status', 'name', 'email', 'phone', 'company', 'country', 'fields', 'message', 'locale', 'source_page',
        'utm', 'meta', 'attachments', 'assigned_to', 'first_action_at', 'due_at', 'ticket_key', 'ticket_url', 'ticket_status',
        'telegram_notified_at', 'notes', 'consent', 'consent_version'];

    protected $casts = ['fields' => 'array', 'utm' => 'array', 'meta' => 'array', 'attachments' => 'array', 'consent' => 'bool',
        'first_action_at' => 'datetime', 'due_at' => 'datetime', 'telegram_notified_at' => 'datetime'];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LeadComment::class)->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logOnly(['status', 'assigned_to', 'ticket_status', 'notes']);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'new' && $this->created_at->lt(now()->subHours(24));
    }

    public function cohortLabel(): string
    {
        return config('flystat.cohorts.'.$this->cohort, $this->cohort);
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            $lead->due_at ??= now()->addHours(24);
        });
        static::updating(function (Lead $lead) {
            if ($lead->isDirty('status') && $lead->getOriginal('status') === 'new' && ! $lead->first_action_at) {
                $lead->first_action_at = now();
            }
        });
    }
}
