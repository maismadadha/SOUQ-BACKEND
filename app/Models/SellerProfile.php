<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    'store_cover_url'];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    public function mainCategory()
{
    return $this->belongsTo(Category::class, 'main_category_id');
}



}
