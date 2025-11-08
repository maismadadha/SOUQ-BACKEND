<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
    'customer_id',
    'store_id',
    'address_id',      // جديد
    'subtotal',
    'delivery_fee',
    'discount_total',
    'total_price',     // جديد
    'items_count',
    'note',
    'status'
];


     public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function store()
    {
        return $this->belongsTo(User::class, 'store_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function delivery()
    {
        return $this->hasOne(DeliveryOrder::class, 'order_id');
    }

    public function address()
{
    return $this->belongsTo(Address::class, 'address_id');
}


}
