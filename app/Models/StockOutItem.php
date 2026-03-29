<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutItem extends Model
{
    use HasFactory;
    protected $table = "stockout_items";
    protected $fillable = [
        'stockout_id', // This is the correct field name
        'product_id',
        'unit_id',
        'quantity',
        'restaurant_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    // Relationships
    public function stockout()
    {
        return $this->belongsTo(StockOut::class, 'stockout_id'); // Specify foreign key
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}