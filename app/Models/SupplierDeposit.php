<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierDeposit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'deposit_date',
        'amount',
        'payment_mode',
        'transaction_no',
        'remarks',
        'restaurant_id',
        'user_id'
    ];

    protected $casts = [
        'deposit_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Payment modes
    const PAYMENT_MODES = [
        'CASH' => 'Cash',
        'UPI' => 'UPI',
        'BANK_TRANSFER' => 'Bank Transfer',
        'CHEQUE' => 'Cheque',
        'OTHER' => 'Other'
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByRestaurant($query)
    {
        return $query->where('restaurant_id', auth()->user()->restaurant_id);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('deposit_date', [$startDate, $endDate]);
    }
}