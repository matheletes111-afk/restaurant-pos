<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItems;

class OrderManage extends Model
{
    use HasFactory;
    protected $table = "orders";
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'table_id',
        'order_type',
        'discount',
        'discount_percentage',
        'total_amount',
        'taxable_amount',
        'gst_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'round_off',
        'amount_paid',
        'payment_status',
        'payment_method',
        'remarks',
        'order_status',
        'user_id',
        'restaurant_id',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'round_off' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(TableManage::class, 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    // Accessor to get formatted discount percentage
    public function getDiscountPercentageFormattedAttribute()
    {
        return $this->discount_percentage ? $this->discount_percentage . '%' : '0%';
    }
}