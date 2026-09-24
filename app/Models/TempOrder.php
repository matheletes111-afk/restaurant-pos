<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempOrder extends Model
{
    protected $table = 'temp_orders';
    protected $fillable = [
        'table_id',
        'order_id',
        'customer_name',
        'customer_phone',
        'order_type',
        'total_amount',
        'taxable_amount',
        'gst_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'discount',
        'discount_percentage',
        'grand_total',
        'round_off',
        'is_gst_bill',
        'restaurant_gst_percentage',
        'restaurant_gstin',
        'remarks',
        'order_status',
        'payment_status',
        'payment_method',
        'amount_paid',
        'is_read',
        'read_at',
        'restaurant_id',
        'user_id',
        'created_by'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function scopePastWeek($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function items()
    {
        return $this->hasMany(TempOrderItem::class, 'temp_order_id');
    }

    public function table_details()
    {
        return $this->hasOne(TableManage::class, 'id', 'table_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
}