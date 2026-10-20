<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['name', 'countries', 'contact_name', 'contact_email', 'contact_phone', 'website', 'logo', 'is_published', 'notes'];

    protected $casts = ['countries' => 'array', 'is_published' => 'bool'];
}
