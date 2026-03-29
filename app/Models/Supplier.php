<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_name',
        'shop_name',
        'phone',
        'email',
        'address',
        'opening_outstanding',
        'current_outstanding',
        'total_deposits',
        'last_deposit_date',
        'last_purchase_date',
        'status',
        'restaurant_id',
        'user_id'
    ];

    protected $casts = [
        'opening_outstanding' => 'decimal:2',
        'current_outstanding' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'last_deposit_date' => 'date',
        'last_purchase_date' => 'date',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function deposits()
    {
        return $this->hasMany(SupplierDeposit::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

    public function scopeByRestaurant($query)
    {
        return $query->where('restaurant_id', auth()->user()->restaurant_id);
    }

    // Helper methods
    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()->sum('total_amount');
    }

    public function getTotalDepositsAttribute()
    {
        return $this->deposits()->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->current_outstanding - $this->getTotalDepositsAttribute();
    }

    public function updateOutstanding($amount, $type = 'purchase')
    {
        if ($type === 'purchase') {
            $this->current_outstanding += $amount;
            $this->last_purchase_date = now();
        } elseif ($type === 'deposit') {
            $this->current_outstanding -= $amount;
            if ($this->current_outstanding < 0) {
                $this->current_outstanding = 0;
            }
            $this->total_deposits += $amount;
            $this->last_deposit_date = now();
        }
        $this->save();
    }
}