<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeWorkingHour extends Model
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
        'employee_id',
        'day_of_week',
        'start_time',
        'end_time'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
