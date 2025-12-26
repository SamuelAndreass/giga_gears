<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_id',

        'tracking_number',
        'shipping_date',
        'estimated_arrival_date',
        'actual_arrival',

        'status',
        'notes',
    ];

    protected $casts = [
        'shipping_date' => 'date',
        'estimated_arrival_date' => 'date',
        'actual_arrival' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // ShippingOrder belongs to an Order
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    // ShippingOrder belongs to Shipping (courier service)
    public function shipping()
    {
        return $this->belongsTo(\App\Models\Shipping::class);
    }
}
