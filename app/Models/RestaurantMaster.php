<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMaster extends Model
{
    use HasFactory;
    
    protected $table = 'restaurant_master';
    
    protected $fillable = [
        'name',
        'address',
        'pincode',
        'gstin',
        'fssai_number',
        'gst_percentage',
        'owner_id',
        'status',
        'created_by',
        'updated_by',
        'qr_code_image',
        'upi_id'
    ];
    
    protected $casts = [
        'gst_percentage' => 'decimal:2'
    ];
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function active_subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')->where('status', 'active');
    }

    public function latest_subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')->latestOfMany();
    }
}