<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppEvent extends Model
{
    protected $table = 'whatsapp_events';

    protected $fillable = [
        'wa_message_id',
        'event_type',
        'sender_phone',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
