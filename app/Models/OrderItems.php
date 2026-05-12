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
        'user_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'item_discount_percentage' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'quantity' => 'integer',
        'is_new' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(OrderManage::class, 'order_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    // Accessor to get item total before GST
    public function getItemSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Accessor to get discounted price for single item
    public function getDiscountedPricePerItemAttribute()
    {
        return $this->discounted_price ?: $this->price;
    }

    // Accessor to get total taxable amount
    public function getTotalTaxableAttribute()
    {
        return $this->taxable_amount ?: ($this->price * $this->quantity);
    }

    // Accessor to get formatted GST rate
    public function getGstRateFormattedAttribute()
    {
        return $this->gst_rate ? $this->gst_rate . '%' : '0%';
    }
}