<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    //
    protected $table = 'shippings';

    protected $fillable = ['name', 'service_type','base_rate','per_kg','min_delivery_days','max_delivery_days','coverage','status','logo'];

    protected $casts = [ 'base_rate' => 'float','per_kg' => 'float', 'min_delivery_days' => 'integer', 'max_delivery_days' => 'integer'];
    public function scopeActive($query){
        return $query->where('status', 'active');
    }

    public function shiporder(){
        return $this->hasMany(ShippingOrder::class);
    }

    public function getDeliveryRangeAttribute(){
        if ($this->min_delivery_days && $this->max_delivery_days) {
            return "{$this->min_delivery_days}–{$this->max_delivery_days} days";
        } elseif ($this->min_delivery_days) {
            return "{$this->min_delivery_days} days";
        }
        return null;
    }

    public function getIsActiveAttribute(){
        return $this->status === 'active';
    }

    public function setServiceTypeAttribute($value){
        $this->attributes['service_type'] = ucwords(strtolower($value));
    }
}