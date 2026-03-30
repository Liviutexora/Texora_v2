<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'status'
    ];

    // Relație cu servicii (many-to-many)
    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    // Helper: nume complet
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
