<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    // أضف image ضمن الأعمدة القابلة للتعبئة
    protected $fillable = ['name', 'image'];

    public function sellers()
    {
        return $this->hasMany(SellerProfile::class, 'main_category_id');
    }
}
