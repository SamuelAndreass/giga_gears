<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    //
    protected $fillable = ['user_id', 'customer_name', 'phone', 'address', 'city', 'postal_code', 'country'];

    public function user(){
        $this->belongsTo(User::class);
    }
}
