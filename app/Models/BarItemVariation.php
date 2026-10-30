<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarItemVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bar_item_variations';
    protected $guarded = [];

    /**
     * Relationship: The Bar Item this variation belongs to
     */
    public function item()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
