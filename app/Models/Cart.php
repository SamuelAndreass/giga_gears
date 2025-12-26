<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'total_price', 'status'];

    protected $casts = ['total_price' => 'decimal:2'];
    public function items(){
        return $this->hasMany(CartItem::class);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }
}
