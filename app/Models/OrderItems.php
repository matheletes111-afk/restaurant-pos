<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;
    protected $table = "order_items";
    protected $fillable = [
        'order_id',
        'subcategory_id',
        'quantity',
        'price',
        'gst_rate',
        'total_amount',
        'order_status',
        'restaurant_id',
        'user_id',
    ];

    public function order()
    {
        return $this->belongsTo(OrderManage::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(SubCategory::class, 'food_item_id');
    }

    public function subcategory()
{
    return $this->belongsTo(SubCategory::class, 'subcategory_id');
}
}
