<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    //
    protected $fillable = ['product_id', 'qty', 'price', 'cart_id', 'subtotal'];

    protected $casts = ['qty' => 'integer', 'price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function cart(){
        return $this->belongsTo(Cart::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
