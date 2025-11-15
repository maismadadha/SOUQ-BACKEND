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

    protected $casts = [
        'attributes' => 'array',
        'price'      => 'decimal:2',
    ];

    // مهم عشان يرجع في الـ JSON
    protected $appends = ['main_image_url'];

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
        // 1) لو عندك cover_image جاهز (زي اللي مبين في JSON)
        if (!empty($this->cover_image)) {
            return $this->cover_image; // لأنه أصلاً URL كامل من Pexels
        }

        // 2) لو ما في cover_image، نرجع لأول صورة من جدول product_images
        $image = $this->images()->first();
        if (!$image) {
            return null;
        }

        $url = $image->image_url;

        // 3) لو الرابط أصلاً كامل (http أو https) → رجّعه زي ما هو
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // 4) غير هيك، اعتبره path نسبي جوّا storage
        return asset('storage/' . ltrim($url, '/'));
    }
}
