<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public $incrementing = false;
    

    protected $table = 'favorites';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'store_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function store()
    {
        return $this->belongsTo(User::class, 'store_id');
    }

}
