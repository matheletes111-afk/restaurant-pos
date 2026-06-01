<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderToPayment extends Model
{
    use HasFactory;
    
    protected $table = 'order_to_payments';
    
    protected $fillable = [
        'order_id',
        'restaurant_id',
        'amount',
        'payment_method',
        'transaction_no',
        'remarks',
        'payment_date',
        'created_by'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];
    
    public function order()
    {
        return $this->belongsTo(OrderManage::class, 'order_id');
    }
    
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}