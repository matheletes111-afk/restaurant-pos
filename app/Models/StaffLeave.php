<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StaffLeave extends Model
{
    use HasFactory;

    protected $table = 'staff_leaves';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'status',
        'cancellation_remarks',
        'cancelled_by',
        'cancelled_at',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'days_count' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Human-readable leave type name.
     */
    public function getLeaveTypeNameAttribute(): string
    {
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
        return $defs[$this->leave_type]['name'] ?? ucwords(str_replace('_', ' ', $this->leave_type));
    }

    /**
     * Human-readable leave type code (e.g. SL, PL, HD, EL).
     */
    public function getLeaveTypeCodeAttribute(): string
    {
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
        return $defs[$this->leave_type]['code'] ?? strtoupper(substr($this->leave_type, 0, 2));
    }

    /**
     * Leave type icon.
     */
    public function getLeaveTypeIconAttribute(): string
    {
        $defs = StaffLeavePolicy::getLeaveTypeDefinitions();
        return $defs[$this->leave_type]['icon'] ?? 'fas fa-calendar-day';
    }

    /**
     * Formatted date range label.
     */
    public function getFormattedDateRangeAttribute(): string
    {
        $start = Carbon::parse($this->start_date)->format('d M Y');
        $end = Carbon::parse($this->end_date)->format('d M Y');
        if ($start === $end) {
            return $start;
        }
        return "{$start} to {$end}";
    }
}

