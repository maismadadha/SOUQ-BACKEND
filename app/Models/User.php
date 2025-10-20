<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table ='users';
    protected $fillable = ['email', 'phone','role'];

    public function customerProfile()
{
        return $this->hasOne(CustomerProfile::class,'user_id');
}

    public function sellerProfile()
{
    return $this->hasOne(SellerProfile::class,'user_id');
}

    public function deliveryProfile()
{
    return $this->hasOne(DeliveryProfile::class,'user_id');
}

    public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
}

    public function storeCategories()
{
    return $this->hasMany(StoreCategory::class, 'store_id');
}

    public function products()
{
    return $this->hasMany(Product::class, 'store_id');
}

    public function customerOrders()
{
    return $this->hasMany(Order::class, 'customer_id');
}

    public function storeOrders()
{
    return $this->hasMany(Order::class, 'store_id');
}

    public function deliveryOrders()
{
    return $this->hasMany(DeliveryOrder::class, 'delivery_id');
}

    public function addresses()
{
    return $this->hasMany(Address::class, 'user_id');
}

    public function favoriteStores()
{
    return $this->belongsToMany(
        User::class,
        'favorites',
        'user_id',
        'store_id'
    );
}

    public function favoritedByUsers()
{
    return $this->belongsToMany(
        User::class,
        'favorites',
        'store_id',
        'user_id'
    );
}

    public function sliderAds()
{
    return $this->hasMany(SliderAd::class, 'store_id');
}

}
