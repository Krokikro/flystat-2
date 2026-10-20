<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Document extends Model
{
    use HasTranslations;

    protected $fillable = ['type', 'slug', 'title', 'body', 'files', 'version', 'effective_at', 'is_published', 'sort'];

    public array $translatable = ['title', 'body'];

    protected $casts = ['files' => 'array', 'effective_at' => 'date', 'is_published' => 'bool'];
}
