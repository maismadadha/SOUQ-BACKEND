<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $table = 'delivery_orders';

    protected $fillable = [
        'order_id',
        'delivery_id',
        'picked_at',
        'delivered_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

     public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    } 
}
