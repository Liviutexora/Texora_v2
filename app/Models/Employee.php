<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
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
        'first_name',
        'last_name',
        'phone',
        'email',
        'status'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'employee_service', 'employee_id', 'service_id')
            ->withPivot('business_id')
            ->wherePivot('business_id', auth()->user()->business_id);
    }

    public function workingHours()
    {
        return $this->hasMany(\App\Models\EmployeeWorkingHour::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
