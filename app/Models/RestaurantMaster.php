<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMaster extends Model
{
    use HasFactory;
    protected $table = "restaurant_master";
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
