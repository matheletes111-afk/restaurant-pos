<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaiterCall extends Model
{
    protected $table = 'waiter_calls';

    protected $fillable = [
        'restaurant_id',
        'order_id',
        'table_name',
        'customer_name',
        'is_attended',
        'attended_at',
        'attended_by',
    ];

    protected $casts = [
        'is_attended' => 'boolean',
        'attended_at' => 'datetime',
    ];
}
