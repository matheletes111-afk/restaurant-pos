<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseRestaurantMapping extends Model
{
    use HasFactory;

    protected $table = 'franchise_restaurant_mappings';

    protected $fillable = [
        'user_id',
        'restaurant_id',
    ];

    /**
     * Franchise Sponsorship Owner (User with role FSO)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mapped Restaurant Master record
     */
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
}

