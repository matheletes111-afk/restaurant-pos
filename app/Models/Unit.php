<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'status',
        'restaurant_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope for active units
    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

        // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}