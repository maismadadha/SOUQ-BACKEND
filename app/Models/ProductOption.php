<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $table = 'product_options';

    protected $fillable = [
        'product_id',
        'name',
        'label',
        'selection',
        'required',
        'sort_order',
        'affects_variant'
    ];

    public function product()
{
        return $this->belongsTo(Product::class, 'product_id');
}

    public function values()
{
        return $this->hasMany(ProductOptionValue::class, 'option_id');
}

}
