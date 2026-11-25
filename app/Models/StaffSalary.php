<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSalary extends Model
{
    use HasFactory;

    protected $table = 'staff_salaries';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'employment_type',
        'monthly_salary',
        'pf_percentage',
        'esi_percentage',
        'ptax_type',
        'ptax_percentage',
        'ptax_amount',
        'other_deduction_type',
        'other_deduction_percentage',
        'other_deduction_amount',
        'weekoff_count',
        'hourly_wage',
        'notes',
    ];

    protected $casts = [
        'monthly_salary' => 'decimal:2',
        'pf_percentage' => 'decimal:2',
        'esi_percentage' => 'decimal:2',
        'ptax_type' => 'string',
        'ptax_percentage' => 'decimal:2',
        'ptax_amount' => 'decimal:2',
        'other_deduction_type' => 'string',
        'other_deduction_percentage' => 'decimal:2',
        'other_deduction_amount' => 'decimal:2',
        'weekoff_count' => 'integer',
        'hourly_wage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    /**
     * Compute breakdown for permanent salary.
     */
    public function getSalaryBreakdownAttribute(): array
    {
        $gross = (float) ($this->monthly_salary ?? 0);
        $pfPct = (float) ($this->pf_percentage ?? 0);
        $esiPct = (float) ($this->esi_percentage ?? 0);

        $pfAmount = round(($gross * $pfPct) / 100, 2);
        $esiAmount = round(($gross * $esiPct) / 100, 2);

        // P-Tax
        if (($this->ptax_type ?? 'percentage') === 'fixed') {
            $ptaxAmount = round((float) ($this->ptax_amount ?? 0), 2);
        } else {
            $ptaxPct = (float) ($this->ptax_percentage ?? 0);
            $ptaxAmount = round(($gross * $ptaxPct) / 100, 2);
        }

        // Other Deduction
        if (($this->other_deduction_type ?? 'percentage') === 'fixed') {
            $otherAmount = round((float) ($this->other_deduction_amount ?? 0), 2);
        } else {
            $otherPct = (float) ($this->other_deduction_percentage ?? 0);
            $otherAmount = round(($gross * $otherPct) / 100, 2);
        }

        $totalDeductions = round($pfAmount + $esiAmount + $ptaxAmount + $otherAmount, 2);
        $netSalary = max(0, round($gross - $totalDeductions, 2));

        return [
            'gross' => $gross,
            'pf_amount' => $pfAmount,
            'esi_amount' => $esiAmount,
            'ptax_amount' => $ptaxAmount,
            'other_amount' => $otherAmount,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
        ];
    }
}

