<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'store_id',
        'store_category_id',
        'name',
        'description',
        'price',
        'preparation_time',
        'attributes'
    ];



    // ✅ أهم نقطة: Laravel يحوّل attributes ↔ JSON لحاله
    protected $casts = [
        'attributes' => 'array',
        'price'      => 'decimal:2', // اختياري
    ];

     public function store()
{
        return $this->belongsTo(User::class, 'store_id');
}

    public function storeCategory()
{
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
}

    public function images()
{
        return $this->hasMany(ProductImage::class, 'product_id');
}

    public function options()
{
        return $this->hasMany(ProductOption::class, 'product_id');
}

    public function variants()
{
        return $this->hasMany(ProductVariant::class, 'product_id');
}

    public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'product_id');
}


}
