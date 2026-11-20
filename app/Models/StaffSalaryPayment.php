<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSalaryPayment extends Model
{
    use HasFactory;

    protected $table = 'staff_salary_payments';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'payment_date',
        'amount',
        'payment_method',
        'period_from',
        'period_to',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date:Y-m-d',
        'period_from' => 'date:Y-m-d',
        'period_to' => 'date:Y-m-d',
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
}
