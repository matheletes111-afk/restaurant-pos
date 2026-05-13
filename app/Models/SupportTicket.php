<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;
    
    protected $table = 'support_tickets';
    
    protected $fillable = [
        'ticket_no',
        'restaurant_id',
        'subject',
        'message',
        'status',
        'priority',
        'created_by',
        'assigned_to'
    ];
    
    // Status constants
    const STATUS_NEW = 'NEW';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_RESOLVED = 'RESOLVED';
    
    // Priority constants
    const PRIORITY_LOW = 'LOW';
    const PRIORITY_MEDIUM = 'MEDIUM';
    const PRIORITY_HIGH = 'HIGH';
    const PRIORITY_URGENT = 'URGENT';
    
    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function comments()
    {
        return $this->hasMany(SupportTicketComment::class, 'ticket_id')->orderBy('created_at', 'asc');
    }
    
    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_NEW => '<span class="btn  btn-sm btn-warning"><i class="fas fa-clock"></i> New</span>',
            self::STATUS_IN_PROGRESS => '<span class="btn  btn-sm btn-info"><i class="fas fa-spinner"></i> In Progress</span>',
            self::STATUS_RESOLVED => '<span class="btn  btn-sm btn-success"><i class="fas fa-check-circle"></i> Resolved</span>',
        ];
        return $badges[$this->status] ?? '<span class="btn  btn-sm btn-secondary">Unknown</span>';
    }
    
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            self::PRIORITY_LOW => '<span class="btn  btn-sm btn-secondary">Low</span>',
            self::PRIORITY_MEDIUM => '<span class="btn  btn-sm btn-primary">Medium</span>',
            self::PRIORITY_HIGH => '<span class="btn  btn-sm btn-warning">High</span>',
            self::PRIORITY_URGENT => '<span class="btn  btn-sm btn-danger">Urgent</span>',
        ];
        return $badges[$this->priority] ?? '<span class="btn  btn-sm btn-secondary">Medium</span>';
    }
}