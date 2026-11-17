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
        'plan_type',
        'label_name',
        'cross_price',
        'gst_percentage',
        'taxable_amount',
        'gst_amount',
        'razorpay_plan_id',
        'status',
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
        'multi_outlet_checkbox',
        'report_management_checkbox',
        'bar_management_checkbox',
        'total_number_of_outlets',
        'is_default_free',
        'is_default_paid'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
