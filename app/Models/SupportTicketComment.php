<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketComment extends Model
{
    use HasFactory;
    
    protected $table = 'support_ticket_comments';
    
    protected $fillable = [
        'ticket_id',
        'user_id',
        'user_type',
        'comment',
        'attachment'
    ];
    
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}