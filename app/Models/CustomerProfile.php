<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    protected $table = 'customer_profiles';

    //نحدد المفتاح الأساسي لأنه مش  ال id
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';     // نوع المفتاح الأساسي رقم صحيح

    protected $fillable = ['user_id', 'first_name', 'last_name'];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}




}
