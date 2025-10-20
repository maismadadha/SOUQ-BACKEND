<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'quantity',
        'variant_key'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function optionValues()
    {
        return $this->hasMany(VariantOptionValue::class, 'variant_id');
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'variant_id');
}

}
