<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableManage extends Model
{
    use HasFactory;
    protected $table = "table_management";
    protected $fillable = ['name', 'description', 'table_category_id', 'status', 'user_id', 'order_id', 'table_status', 'restaurant_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(TableCategory::class, 'table_category_id');
    }

    public function tableCategory()
    {
        return $this->belongsTo(TableCategory::class, 'table_category_id');
    }

    public function getCategoryNameAttribute(): string
    {
        return ($this->category && $this->category->status !== 'D') ? $this->category->name : 'General Category';
    }

    // 🔗 One Table has many Orders
    public function orders()
    {
        return $this->hasMany(OrderManage::class, 'table_id');
    }

    public function order()
    {
        return $this->hasOne('App\Models\OrderManage','id','order_id');
    }

    public function activeOrders()
    {
        return $this->hasMany(OrderManage::class, 'table_id')->where('order_complete', '!=', 'DONE');
    }
}
