<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'name',
        'razorpay_plan_id',
        'status',
        'price',
        'country_id',
        'currency',
        'billing_cycle',
        'duration_days',
        'description',
        'is_default_free',
        'is_default_paid'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}