<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WasteInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'waste_inventories';

    protected $fillable = [
        'waste_no',
        'waste_date',
        'item_type',
        'product_id',
        'sub_category_id',
        'unit_id',
        'waste_qty',
        'current_stock_before',
        'reason',
        'status',
        'restaurant_id',
        'user_id',
    ];

    protected $casts = [
        'waste_date'           => 'date',
        'waste_qty'            => 'decimal:2',
        'current_stock_before' => 'decimal:2',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function barItem()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    // Scopes
    public function scopeByRestaurant($query, $restaurantId = null)
    {
        $id = $restaurantId ?: (auth()->check() ? auth()->user()->restaurant_id : null);
        return $query->where('restaurant_id', $id);
    }

    /**
     * Generate next automatic waste number, e.g. WST-261102-001
     */
    public static function generateWasteNo($restaurantId)
    {
        $today = Carbon::now()->format('ymd');
        $prefix = 'WST-' . $today . '-';

        $lastRecord = self::withTrashed()
            ->where('restaurant_id', $restaurantId)
            ->where('waste_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->waste_no, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}

