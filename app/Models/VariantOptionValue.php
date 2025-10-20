<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantOptionValue extends Model
{
    protected $table = 'variant_option_values';
    public $timestamps = false;

    protected $fillable = [
        'variant_id',
        'product_option_id',
        'product_option_value_id'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function value()
    {
        return $this->belongsTo(ProductOptionValue::class, 'product_option_value_id');
    }
}
