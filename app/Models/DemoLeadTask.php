<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemoLeadTask extends Model
{
    use HasFactory;

    protected $table = 'demo_lead_tasks';

    protected $fillable = [
        'demo_lead_id',
        'task_title',
        'due_date',
        'description',
        'is_completed',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function lead()
    {
        return $this->belongsTo(DemoLead::class, 'demo_lead_id');
    }
}
