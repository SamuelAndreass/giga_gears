<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['user_id','order_code','ordered_at','shipping_address','payment_method',
        'payment_status','status','subtotal','shipping_fee','tax','discount','total_amount','idempotency_key','shipping_id'];

    public function items(){
        return $this->hasmany(OrderItem::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shiporder(){
        return $this->hasMany(ShippingOrder::class);
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($order) {
            $last = self::latest('id')->first();
            $next = $last ? $last->id + 1 : 1;
            $order->order_code = 'ORD-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }
}
