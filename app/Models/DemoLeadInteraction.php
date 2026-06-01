<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemoLeadInteraction extends Model
{
    use HasFactory;

    protected $table = 'demo_lead_interactions';

    protected $fillable = [
        'demo_lead_id',
        'user_id',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(DemoLead::class, 'demo_lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
