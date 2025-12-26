<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\CustomerProfile;
class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_seller',
    ];

    protected $casts = [
        'is_seller' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role === 'customer') {
                CustomerProfile::create([
                    'user_id' => $user->id,
                    'phone' => $user->phone,
                ]);
            }
        });
    }

    public function scopeActive($q) {
        return $q->where('status','active')
                 ->where(function($s){
                     $s->whereNull('suspended_until')
                       ->orWhere('suspended_until','<',now());
                 });
    }

    public function getAvatarUrlAttribute()
    {
        $path = $this->customerProfile?->avatar_path ?? null;

        if ($path) {
            return Storage::url($path);
        }

        return asset('images/default-avatar.png');
    }
    public function customerProfile(){
        return $this->hasOne(CustomerProfile::class);
    }

    public function sellerStore(){ return $this->hasOne(SellerStore::class);}
    
    public function cart(){ return $this->hasOne(Cart::class);}

    public function storeReview(){ return $this->hasMany(SellerStore::class);}

    public function productReview() {return $this->hasMany(ProductReview::class);}

}
