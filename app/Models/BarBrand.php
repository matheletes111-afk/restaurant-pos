<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarBrand extends Model
{
    use HasFactory;

    protected $table = 'bar_brands';
    protected $guarded = [];

    /**
     * Relationship: Items belonging to this brand
     */
    public function items()
    {
        return $this->hasMany(SubCategory::class, 'brand_id')->where('status', '!=', 'D');
    }
}
