<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email'
    ];
    protected static function booted()
{
    static::addGlobalScope('business', function ($query) {
        if (auth()->check()) {
            $query->where('business_id', auth()->user()->business_id);
        }
    });
}
}

