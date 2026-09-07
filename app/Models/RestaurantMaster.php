<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMaster extends Model
{
    use HasFactory;
    
    protected $table = 'restaurant_master';
    
    protected $fillable = [
        'restaurant_id_unique',
        'name',
        'address',
        'pincode',
        'gstin',
        'fssai_number',
        'gst_percentage',
        'owner_id',
        'status',
        'created_by',
        'updated_by',
        'qr_code_image',
        'upi_id'
    ];
    
    protected $casts = [
        'gst_percentage' => 'decimal:2'
    ];

    /**
     * Booted model hooks
     */
    protected static function booted()
    {
        static::creating(function ($restaurant) {
            if (empty($restaurant->restaurant_id_unique)) {
                $restaurant->restaurant_id_unique = static::generateUniqueRestaurantId();
            }
        });
    }

    /**
     * Generate next sequential unique restaurant ID (e.g. BILL-BITE-001)
     */
    public static function generateUniqueRestaurantId(): string
    {
        $existing = static::whereNotNull('restaurant_id_unique')
            ->pluck('restaurant_id_unique');

        $maxNum = 0;
        foreach ($existing as $val) {
            if (preg_match('/BILL-BITE-(\d+)/i', $val, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum + 1;
        return 'BILL-BITE-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(OrderManage::class, 'restaurant_id');
    }

    public function tables()
    {
        return $this->hasMany(TableManage::class, 'restaurant_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'restaurant_id');
    }

    public function active_subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')
            ->whereIn('status', ['active', 'completed']);
    }

    public function latest_subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')->latestOfMany();
    }
}