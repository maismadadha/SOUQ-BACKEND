<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'products';
    protected $appends = [
    'main_image_url',
    'cover_image',
];



    protected $fillable = [
        'store_id',
        'store_category_id',
        'name',
        'description',
        'price',
        'preparation_time',
        'attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
        'price'      => 'decimal:2',
    ];

    // مهم عشان يرجع في الـ JSON


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

    // ========= أهم جزء: main_image_url =========

  public function getMainImageUrlAttribute()
{
    // خذ أول صورة من product_images
    $firstImage = $this->images()->first();

    if (!$firstImage) {
        return null;
    }

    $value = $firstImage->image_url;

    // إذا صورة من النت (Seeder)
    if (Str::startsWith($value, ['http://', 'https://'])) {
        return $value;
    }

    // إذا صورة مرفوعة من النظام
    return asset('storage/' . $value);
}

public function getCoverImageAttribute()
{
    // 1️⃣ لو في صورة كفر مخزنة (Seeder قديم)
    if (!empty($this->attributes['cover_image'])) {
        $value = $this->attributes['cover_image'];

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    // 2️⃣ خذ أول صورة من product_images
    $firstImage = $this->images()->first();
    if ($firstImage) {
        $value = $firstImage->image_url;

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    // 3️⃣ صورة افتراضية
    return asset('images/default-product.png');
}


}
