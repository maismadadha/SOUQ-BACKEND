<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'city_name',
        'street',
        'building_number',
        'address_note',
        'latitude',
        'longitude',
        'address_name',
    ];

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'address_id');
    }

}
