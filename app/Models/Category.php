<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     protected $table = 'categories';

     protected $fillable = ['name'];

     public function sellers()
    {
        return $this->hasMany(SellerProfile::class, 'main_category_id');
    }
}
