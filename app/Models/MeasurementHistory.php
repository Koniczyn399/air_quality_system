<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementHistory extends Model
{
    protected $table = 'measurement_histories';

    protected $fillable = [
        'measurement_device_id',
        'status',  
        'changed_by',
        'notes'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(MeasurementDevice::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}