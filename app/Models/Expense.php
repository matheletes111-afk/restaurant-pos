<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'amount',
        'description',
        'expense_date',
        'payment_method',
        'restaurant_id',
        'created_by'
    ];

   

    protected $casts = [
        'amount' => 'decimal:2',
        
    ];

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}