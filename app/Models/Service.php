<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('business', function ($query) {
            if (auth()->check()) {
                $query->where('services.business_id', auth()->user()->business_id);
            }
        });
    }

    protected $fillable = [
        'business_id',
        'name',
        'duration',
        'price',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class)
            ->when(auth()->check(), function ($query) {
                $query->wherePivot('business_id', auth()->user()->business_id);
            });
    }
}
