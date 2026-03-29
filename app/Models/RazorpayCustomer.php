<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayCustomer extends Model
{
    use HasFactory;

    protected $table = 'razorpay_customers';
    
    protected $fillable = [
        'user_id',
        'rzpay_customer_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}