<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingNotification extends Model
{
    use HasFactory;

    protected $table = 'marketing_notifications';

    protected $fillable = [
        'title',
        'description',
        'scheduled_at',
        'status',
        'target_type',
        'total_recipients',
        'sent_count',
        'failed_count',
        'created_by',
        'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
    ];

    /**
     * Recipients associated with this notification broadcast
     */
    public function recipients()
    {
        return $this->hasMany(MarketingNotificationRecipient::class, 'notification_id');
    }

    /**
     * Admin user who created the broadcast
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to find due scheduled campaigns
     */
    public function scopeScheduledDue($query)
    {
        return $query->whereIn('status', ['scheduled', 'pending'])
                     ->where(function($q) {
                         $q->whereNull('scheduled_at')
                           ->orWhere('scheduled_at', '<=', now());
                     });
    }

    /**
     * Get percentage progress
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_recipients <= 0) {
            return 100;
        }

        $processed = $this->sent_count + $this->failed_count;
        return min(100, (int) round(($processed / $this->total_recipients) * 100));
    }

    /**
     * Get human status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        switch ($this->status) {
            case 'completed':
                if ($this->failed_count > 0 && $this->sent_count > 0) {
                    return ['class' => 'bg-warning text-dark', 'label' => 'Partially Sent'];
                }
                return ['class' => 'bg-success text-white', 'label' => 'Delivered'];
            case 'processing':
                return ['class' => 'bg-info text-white', 'label' => 'Sending in Background...'];
            case 'scheduled':
                return ['class' => 'bg-primary text-white', 'label' => 'Scheduled'];
            case 'failed':
                return ['class' => 'bg-danger text-white', 'label' => 'Failed'];
            case 'draft':
                return ['class' => 'bg-secondary text-white', 'label' => 'Draft'];
            default:
                return ['class' => 'bg-secondary text-white', 'label' => ucfirst($this->status)];
        }
    }
}
