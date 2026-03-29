<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItems;

class OrderManage extends Model
{
    use HasFactory;
    protected $table = "orders";
    protected $fillable = [
        'customer_name',
        'table_id',
        'discount',
        'status',
    ];
    public function table()
    {
        return $this->belongsTo(TableManage::class, 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }



    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }


    


}
