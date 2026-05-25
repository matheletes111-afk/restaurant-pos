<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'razorpay_subscription_id',
        'razorpay_plan_id',
        'status',
        'start_date',
        'end_date',
        'renewal_date',
        'auto_renew',
        'refund_amount'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'renewal_date' => 'datetime',
        'auto_renew' => 'boolean',
        'refund_amount' => 'decimal:2',
    ];

    
    public function restaurant_details()
    {
        return $this->hasOne('App\Models\RestaurantMaster','id','user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}