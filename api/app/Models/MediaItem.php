<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class MediaItem extends Model
{
    use HasTranslations;

    protected $fillable = ['path', 'disk', 'kind', 'folder', 'alt', 'tags', 'is_generated', 'width', 'height', 'size'];

    public array $translatable = ['alt'];

    protected $casts = ['tags' => 'array', 'is_generated' => 'bool'];

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
