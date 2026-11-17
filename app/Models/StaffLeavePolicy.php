<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLeavePolicy extends Model
{
    use HasFactory;

    protected $table = 'staff_leave_policies';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'year',
        'leave_type',
        'annual_quota',
        'carried_forward',
        'used_count',
        'is_carry_forward',
        'max_carry_forward',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'annual_quota' => 'decimal:2',
        'carried_forward' => 'decimal:2',
        'used_count' => 'decimal:2',
        'is_carry_forward' => 'boolean',
        'max_carry_forward' => 'integer',
    ];

    public static function getLeaveTypeDefinitions(): array
    {
        return [
            'sick_leave' => [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'icon' => 'fas fa-notes-medical',
                'color' => '#ef4444',
                'bg_color' => '#fef2f2',
                'border_color' => '#fecaca',
                'default_quota' => 0,
                'default_carry_forward' => false,
                'unit' => 'Days',
                'description' => 'Medical and health recovery leave. Unused leaves can be carried forward if enabled.'
            ],
            'paid_leave' => [
                'name' => 'Paid Leave',
                'code' => 'PL',
                'icon' => 'fas fa-calendar-check',
                'color' => '#10b981',
                'bg_color' => '#ecfdf5',
                'border_color' => '#a7f3d0',
                'default_quota' => 0,
                'default_carry_forward' => false,
                'unit' => 'Days',
                'description' => 'Annual earned vacation and personal leave with 100% salary compensation.'
            ],
            'early_leave' => [
                'name' => 'Early Leave',
                'code' => 'EL',
                'icon' => 'fas fa-door-open',
                'color' => '#f59e0b',
                'bg_color' => '#fffbeb',
                'border_color' => '#fde68a',
                'default_quota' => 0,
                'default_carry_forward' => false,
                'unit' => 'Occasions',
                'description' => 'Permitted early shift departures or late arrivals for personal errands without penalty.'
            ],
            'half_day' => [
                'name' => 'Half Day',
                'code' => 'HD',
                'icon' => 'fas fa-hourglass-half',
                'color' => '#8b5cf6',
                'bg_color' => '#f5f3ff',
                'border_color' => '#ddd6fe',
                'default_quota' => 0,
                'default_carry_forward' => false,
                'unit' => 'Sessions',
                'description' => 'Single session attendance (Morning or Afternoon) credited as 0.5 workday.'
            ]
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function getTotalEntitledAttribute(): float
    {
        return round((float) ($this->annual_quota ?? 0) + (float) ($this->carried_forward ?? 0), 2);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, round($this->total_entitled - (float) ($this->used_count ?? 0), 2));
    }

    public function getDefinitionAttribute(): array
    {
        $defs = self::getLeaveTypeDefinitions();
        return $defs[$this->leave_type] ?? [
            'name' => ucwords(str_replace('_', ' ', $this->leave_type)),
            'code' => strtoupper(substr($this->leave_type, 0, 2)),
            'icon' => 'fas fa-calendar',
            'color' => '#64748b',
            'bg_color' => '#f8fafc',
            'border_color' => '#e2e8f0',
            'default_quota' => 0,
            'default_carry_forward' => false,
            'unit' => 'Days',
            'description' => ''
        ];
    }
}

