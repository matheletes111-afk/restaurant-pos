<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingNotificationRecipient extends Model
{
    use HasFactory;

    protected $table = 'marketing_notification_recipients';

    protected $fillable = [
        'notification_id',
        'restaurant_id',
        'restaurant_name',
        'owner_id',
        'owner_name',
        'email',
        'status',
        'error_message',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(MarketingNotification::class, 'notification_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
