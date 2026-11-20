<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPayroll extends Model
{
    use HasFactory;

    protected $table = 'staff_payrolls';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'year',
        'month',
        'total_days',
        'present_days',
        'leave_days',
        'leave_breakdown',
        'absent_days',
        'base_salary',
        'absent_deduction',
        'earned_salary',
        'pf_amount',
        'esi_amount',
        'other_deductions',
        'net_salary',
        'extra_earnings',
        'extra_deductions',
        'advance_amount',
        'final_salary',
        'actual_payable',
        'is_paid',
        'paid_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_days' => 'integer',
        'present_days' => 'decimal:2',
        'leave_days' => 'decimal:2',
        'leave_breakdown' => 'array',
        'absent_days' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'absent_deduction' => 'decimal:2',
        'earned_salary' => 'decimal:2',
        'pf_amount' => 'decimal:2',
        'esi_amount' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'extra_earnings' => 'decimal:2',
        'extra_deductions' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'final_salary' => 'decimal:2',
        'actual_payable' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_date' => 'date:Y-m-d',
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

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
