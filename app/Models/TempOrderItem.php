<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempOrderItem extends Model
{
    protected $table = 'temp_order_items';
    protected $fillable = [
        'temp_order_id',
        'subcategory_id',
        'quantity',
        'price',
        'discounted_price',
        'item_discount_percentage',
        'taxable_amount',
        'gst_rate',
        'gst_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_amount',
        'order_status',
        'is_new',
        'restaurant_id',
        'user_id'
    ];

    public function menuItem()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }
}