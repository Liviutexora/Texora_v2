<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    protected static function booted()
    {
        static::addGlobalScope('business', function ($query) {
            if (auth()->check()) {
                $query->where('business_id', auth()->user()->business_id);
            }
        });
    }

    protected $fillable = [
        'business_id',
        'employee_id',
        'client_id',
        'service_id',
        'start_time',
        'end_time',
        'notes',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
