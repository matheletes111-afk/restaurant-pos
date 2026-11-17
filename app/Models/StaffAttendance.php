<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendances';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'attendance_date',
        'attendance_type',
        'login_time',
        'logout_time',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'attendance_date' => 'date:Y-m-d',
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

    /**
     * Human-readable label for attendance type.
     */
    public function getTypeLabelAttribute(): string
    {
        switch ($this->attendance_type) {
            case 'H':
                return 'Half Day';
            case 'EL':
                return 'Early Leave';
            case 'WO':
                return 'Weekoff';
            case 'P':
            default:
                return 'Full Day Present';
        }
    }

    /**
     * Badge CSS class for attendance type.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        switch ($this->attendance_type) {
            case 'H':
                return 'badge-attendance-halfday';
            case 'EL':
                return 'badge-attendance-earlyleave';
            case 'WO':
                return 'badge-attendance-weekoff';
            case 'P':
            default:
                return 'badge-attendance-present';
        }
    }

    /**
     * Formatted Login Time (e.g. 09:30 AM).
     */
    public function getFormattedLoginTimeAttribute(): string
    {
        if (empty($this->login_time)) {
            return '--:--';
        }
        try {
            return Carbon::parse($this->login_time)->format('h:i A');
        } catch (\Exception $e) {
            return (string) $this->login_time;
        }
    }

    /**
     * Formatted Logout Time (e.g. 06:30 PM).
     */
    public function getFormattedLogoutTimeAttribute(): string
    {
        if (empty($this->logout_time)) {
            return '--:--';
        }
        try {
            return Carbon::parse($this->logout_time)->format('h:i A');
        } catch (\Exception $e) {
            return (string) $this->logout_time;
        }
    }

    /**
     * Work duration calculated if both login and logout time are provided.
     */
    public function getWorkDurationAttribute(): ?string
    {
        if (empty($this->login_time) || empty($this->logout_time)) {
            return null;
        }

        try {
            $dateStr = $this->attendance_date ? Carbon::parse($this->attendance_date)->format('Y-m-d') : date('Y-m-d');
            $start = Carbon::parse($dateStr . ' ' . $this->login_time);
            $end = Carbon::parse($dateStr . ' ' . $this->logout_time);

            if ($end->lt($start)) {
                $end->addDay(); // Shift crossed midnight
            }

            $diffMinutes = $start->diffInMinutes($end);
            $hours = floor($diffMinutes / 60);
            $mins = $diffMinutes % 60;

            if ($hours > 0 && $mins > 0) {
                return "{$hours}h {$mins}m";
            } elseif ($hours > 0) {
                return "{$hours}h";
            }
            return "{$mins}m";
        } catch (\Exception $e) {
            return null;
        }
    }
}

