<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReview extends Model
{
    protected $fillable = ['seller_profiles_id', 'user_id', 'rating', 'comment', 'is_verified'];
    protected $casts = ['rating' => 'integer', 'is_verified' => 'boolean'];

    public function store(){ return $this->belongsTo(SellerStore::class);}
    public function user(){ return $this->belongsTo(User::class);}

}
