<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_no',
        'purchase_date',
        'supplier_id',
        'total_items',
        'bill_amount',
        'total_amount',
        'bill_attachment',
        'remarks',
        'restaurant_id',
        'user_id',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'bill_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate total quantity
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    // Calculate total price
    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->quantity * $item->price;
        });
    }
}