<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempOrderItem extends Model
{
    protected $table = 'temp_order_items';
    protected $fillable = [
        'temp_order_id','subcategory_id','quantity','price','gst_rate','total_amount',
        'order_status','restaurant_id','user_id'
    ];
    public function menuItem()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }
}
