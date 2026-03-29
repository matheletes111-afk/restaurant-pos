<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempOrder extends Model
{
    protected $table = 'temp_orders';
    protected $fillable = [
        'table_id','customer_name','customer_phone','order_type','total_amount',
        'gst_amount','grand_total','discount','remarks','order_status','restaurant_id','user_id'
    ];

    public function items()
    {
        return $this->hasMany(TempOrderItem::class, 'temp_order_id');
    }

    public function table_details()
    {
        return $this->hasOne('App\Models\TableManage','id','table_id');
    }

     public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
}


