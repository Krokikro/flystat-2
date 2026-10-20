<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations, LogsActivity;

    protected $fillable = ['slug', 'template', 'title', 'nav_label', 'seo', 'status', 'in_header', 'in_footer', 'menu_group', 'sort', 'published_at'];

    public array $translatable = ['title', 'nav_label'];

    protected $casts = ['seo' => 'array', 'in_header' => 'bool', 'in_footer' => 'bool', 'published_at' => 'datetime'];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('sort');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logFillable();
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function path(string $locale): string
    {
        return '/'.$locale.($this->slug === '' ? '' : '/'.$this->slug);
    }
}
