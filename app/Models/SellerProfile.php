<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SellerProfile extends Model
{
    protected $table = 'seller_profiles';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'password',
        'name',
        'store_description',
        'main_category_id',
        'store_logo_url',
        'store_cover_url',
        'preparation_days',
        'preparation_hours',
        'delivery_price',
    ];

    protected $casts = [
        'preparation_days'  => 'integer',
        'preparation_hours' => 'integer',
        'delivery_price'    => 'decimal:2',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /* =======================
     *  Relations
     * ======================= */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }

    /* =======================
     *  Accessors (URL)
     * ======================= */
    public function getStoreLogoUrlAttribute($value)
    {
        if (!$value) return null;

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    public function getStoreCoverUrlAttribute($value)
    {
        if (!$value) return null;

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset('storage/' . $value);
    }
}
