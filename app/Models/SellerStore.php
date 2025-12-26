<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Shipping;
class SellerStore extends Model
{
    //
    protected $fillable = ['user_id','store_logo','store_banner','store_name','store_phone','store_address','store_description','province','city','country','address','contact_number','business_category','open_time','close_time','status','verified_at','verified_by'];


    public function user(){ return $this->belongsTo(User::class); }   

    public function products(){ return $this->hasMany(Product::class);}

    public function reviews()  { return $this->hasMany(StoreReview::class);}


    public function shippings(){
        return $this->belongsToMany(Shipping::class, 'seller_courier')
                    ->withPivot(['enabled','extra_fee','settings'])
                    ->withTimestamps();
    }

    public function approve($adminId){
        $this->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $adminId,
        ]);
    }

    public function suspend($adminId){
        $this->update([
            'status' => 'suspend',
            'verified_at' => now(),
            'verified_by' => $adminId,
        ]);
    }
    use HasFactory;
}
