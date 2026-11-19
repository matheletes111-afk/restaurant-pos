<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffExtra extends Model
{
    use HasFactory;

    protected $table = 'staff_extras';

    public const TYPE_EARNING = 'Earning';
    public const TYPE_DEDUCTION = 'Deduction';
    public const TYPE_ADVANCE = 'Advance';

    public static function getValidTypes(): array
    {
        return [
            self::TYPE_EARNING,
            self::TYPE_DEDUCTION,
            self::TYPE_ADVANCE,
        ];
    }

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'type',
        'amount',
        'entry_date',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date:Y-m-d',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get badge configuration for type.
     */
    public function getTypeBadgeAttribute(): array
    {
        switch ($this->type) {
            case self::TYPE_EARNING:
                return [
                    'label' => 'Earning',
                    'class' => 'bg-success text-white',
                    'icon' => 'fa-arrow-trend-up',
                    'sign' => '+',
                    'color' => '#10b981',
                ];
            case self::TYPE_DEDUCTION:
                return [
                    'label' => 'Deduction',
                    'class' => 'bg-danger text-white',
                    'icon' => 'fa-arrow-trend-down',
                    'sign' => '-',
                    'color' => '#ef4444',
                ];
            case self::TYPE_ADVANCE:
                return [
                    'label' => 'Advance',
                    'class' => 'bg-warning text-dark',
                    'icon' => 'fa-hand-holding-dollar',
                    'sign' => '•',
                    'color' => '#f59e0b',
                ];
            default:
                return [
                    'label' => $this->type,
                    'class' => 'bg-secondary text-white',
                    'icon' => 'fa-circle-info',
                    'sign' => '',
                    'color' => '#6b7280',
                ];
        }
    }
}
