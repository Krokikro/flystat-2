<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    protected $fillable = ['page_id', 'type', 'anchor', 'data', 'enabled', 'sort'];

    protected $casts = ['data' => 'array', 'enabled' => 'bool'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
