<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryProfile extends Model
{
    protected $table = 'delivery_profiles';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';



    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'password',
        'profile_pic_url'];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}




}
