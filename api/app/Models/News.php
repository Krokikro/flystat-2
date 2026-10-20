<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class News extends Model
{
    use HasTranslations, LogsActivity;

    protected $table = 'news';

    protected $fillable = ['slug', 'title', 'excerpt', 'body', 'cover', 'category', 'tags', 'author', 'seo', 'status', 'published_at', 'telegram_posted_at', 'legacy_id'];

    public array $translatable = ['slug', 'title', 'excerpt', 'body'];

    protected $casts = ['tags' => 'array', 'seo' => 'array', 'published_at' => 'datetime', 'telegram_posted_at' => 'datetime'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logOnly(['title', 'status', 'published_at']);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
