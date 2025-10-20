<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'store_id',
        'store_categories_id',
        'name',
        'description',
        'price',
        'preparation_time',
        'attributes'
    ];

     public function store()
{
        return $this->belongsTo(User::class, 'store_id');
}

    public function storeCategory()
{
        return $this->belongsTo(StoreCategory::class, 'store_categories_id');
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
