<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;

    protected $fillable = ['code', 'name', 'region', 'status', 'is_priority'];

    public array $translatable = ['name'];

    protected $casts = ['is_priority' => 'bool'];
}
