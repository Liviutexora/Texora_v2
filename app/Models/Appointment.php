<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'client_name',
        'service',
        'appointment_time',
        'notes',
        'business_id',
    ];

    // relație cu business (pentru viitor)
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
