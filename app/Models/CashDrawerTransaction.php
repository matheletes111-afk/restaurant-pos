<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashDrawerTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cash_drawer_transactions';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'transaction_type',
        'entry_type',
        'amount',
        'entry_date',
        'entry_time',
        'reference_id',
        'reference_type',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];

    // Transaction Types
    const TYPE_OPENING = 'OPENING';
    const TYPE_CASH_IN = 'CASH_IN';
    const TYPE_CASH_OUT = 'CASH_OUT';
    const TYPE_ORDER_PAYMENT = 'ORDER_PAYMENT';
    const TYPE_SUPPLIER_PAYMENT = 'SUPPLIER_PAYMENT';
    const TYPE_EXPENSE_PAYMENT = 'EXPENSE_PAYMENT';

    // Entry Types
    const ENTRY_CREDIT = 'CREDIT'; // Cash In / Received
    const ENTRY_DEBIT = 'DEBIT';   // Cash Out / Spent

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    // Scopes
    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'ALL') {
            return $query->where('transaction_type', $type);
        }
        return $query;
    }

    /**
     * Get badge configuration for transaction type.
     */
    public function getTypeBadge()
    {
        return match($this->transaction_type) {
            self::TYPE_OPENING => [
                'label' => 'Opening Cash',
                'class' => 'badge-info bg-info text-white',
                'icon' => 'fas fa-door-open',
            ],
            self::TYPE_CASH_IN => [
                'label' => 'Cash In (Added)',
                'class' => 'badge-success bg-success text-white',
                'icon' => 'fas fa-arrow-down',
            ],
            self::TYPE_CASH_OUT => [
                'label' => 'Cash Out (Spend)',
                'class' => 'badge-warning bg-warning text-dark',
                'icon' => 'fas fa-arrow-up',
            ],
            self::TYPE_ORDER_PAYMENT => [
                'label' => 'Order Payment',
                'class' => 'badge-primary bg-primary text-white',
                'icon' => 'fas fa-receipt',
            ],
            self::TYPE_SUPPLIER_PAYMENT => [
                'label' => 'Supplier Deposit',
                'class' => 'badge-danger bg-danger text-white',
                'icon' => 'fas fa-truck',
            ],
            self::TYPE_EXPENSE_PAYMENT => [
                'label' => 'Expense',
                'class' => 'badge-danger bg-danger text-white',
                'icon' => 'fas fa-wallet',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', strtolower($this->transaction_type))),
                'class' => 'badge-secondary bg-secondary text-white',
                'icon' => 'fas fa-money-bill',
            ],
        };
    }
}
