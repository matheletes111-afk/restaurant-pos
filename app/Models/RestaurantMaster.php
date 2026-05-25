<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMaster extends Model
{
    use HasFactory;
    
    protected $table = 'restaurant_master';
    
    protected $fillable = [
        'name',
        'address',
        'pincode',
        'gstin',
        'gst_percentage',
        'owner_id',
        'status',
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'gst_percentage' => 'decimal:2'
    ];
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}