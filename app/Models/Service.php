<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'duration',
        'price',
    ];

    // Relație cu angajați (many-to-many)
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }
    protected static function booted()
{
    static::addGlobalScope('business', function ($query) {
        if (auth()->check()) {
            $query->where('business_id', auth()->user()->business_id);
        }
    });
}
}
