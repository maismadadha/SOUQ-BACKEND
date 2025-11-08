<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    protected $table = 'store_categories';

    protected $fillable = [
        'name',
        'store_id',
    ];

     public function store()
    {
        return $this->belongsTo(User::class, 'store_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'store_category_id');
    }
}
