<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantToCustomPlan extends Model
{
    use HasFactory;
    
    protected $table = 'restaurant_to_custom_plan';
    
    protected $fillable = [
        'restaurant_id',
        'plan_id',
        'created_by'
    ];
    
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
    
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}