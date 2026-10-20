<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;

    protected $fillable = ['category', 'question', 'answer', 'pages', 'is_published', 'sort'];

    public array $translatable = ['question', 'answer'];

    protected $casts = ['pages' => 'array', 'is_published' => 'bool'];
}
