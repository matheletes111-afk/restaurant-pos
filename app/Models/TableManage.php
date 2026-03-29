<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableManage extends Model
{
    use HasFactory;
    protected $table = "table_management";
    protected $fillable = ['name', 'description', 'status','user_id','order_id','table_status','restaurant_id'];
    // 🔗 One Table has many Orders
   public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(OrderManage::class, 'table_id');
    }

    public function order()
    {
        return $this->hasOne('App\Models\OrderManage','id','order_id');
    }
}
