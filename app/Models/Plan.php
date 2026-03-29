<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

protected $fillable = [
    'plan_parent_id',
    'name',
    'price',
    'country_id',
    'currency',
    'billing_cycle',
    'duration_days',
    'description',
    'category_number',
    'total_number_of_dishes',
    'total_number_of_table',
    'inventory_checkbox',
    'is_default_free',
    'is_default_paid',
    'razorpay_plan_id',
    'is_delete',
    'end_date'
];

    protected $casts = [
        'price' => 'decimal:2',
        'end_date' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Plan::class, 'plan_parent_id');
    }

    public function children()
    {
        return $this->hasMany(Plan::class, 'plan_parent_id');
    }

    public function planHistories()
    {
        return $this->hasMany(PlanHistory::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}