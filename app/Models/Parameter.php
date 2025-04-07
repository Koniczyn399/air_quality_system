<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parameter extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'description'
    ];

    public function values(): HasMany
    {
        return $this->hasMany(Value::class);
    }
}