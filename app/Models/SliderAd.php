<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderAd extends Model
{
    protected $table = 'slider_ads';

    protected $fillable = [
        'store_id',
        'title',
        'description', 
        'image_url',
        'start_date',
        'end_date',
    ];

    public function store()
    {
        return $this->belongsTo(User::class, 'store_id');
    }
}
