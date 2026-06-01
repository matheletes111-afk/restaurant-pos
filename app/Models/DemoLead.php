<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemoLead extends Model
{
    use HasFactory;

    protected $table = 'demo_leads';

    protected $fillable = [
        'full_name',
        'restaurant_name',
        'phone_number',
        'email_address',
        'source',
        'status',
        'followup_date',
        'followup_notes',
    ];

    protected $casts = [
        'followup_date' => 'datetime',
    ];

    public function interactions()
    {
        return $this->hasMany(DemoLeadInteraction::class, 'demo_lead_id');
    }

    public function tasks()
    {
        return $this->hasMany(DemoLeadTask::class, 'demo_lead_id');
    }
}
