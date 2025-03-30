<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementDeviceStatusHistory extends Model
{
    protected $fillable = [
        'measurement_device_id',
        'status',  // Make sure status is included here
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