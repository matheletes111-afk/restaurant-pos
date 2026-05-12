<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryManagement extends Model
{
    use HasFactory;
    
    protected $table = 'enquiry_management';
    
    protected $fillable = [
        'restaurant_id',
        'query',
        'created_by',
        'status',
        'query_reply',
        'replier_by'
    ];
    
    // Status constants
    const STATUS_NEW = 'NEW';     // New enquiry, awaiting response
    const STATUS_AT = 'AT';       // Action Taken (Replied/Resolved)
    
    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function replier()
    {
        return $this->belongsTo(User::class, 'replier_by');
    }
    
    // Accessors
    public function getStatusBadgeAttribute()
    {
        if ($this->status == self::STATUS_NEW) {
            return '<span class="badge badge-warning"><i class="fas fa-clock"></i> New</span>';
        }
        return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Action Taken</span>';
    }
    
    public function getStatusTextAttribute()
    {
        return $this->status == self::STATUS_NEW ? 'New' : 'Action Taken';
    }
}