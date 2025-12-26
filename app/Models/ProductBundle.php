<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'bundle_product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

