<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebitNote extends Model
{
    protected $table = 'debit_notes';
    
    protected $fillable = [
        'debit_note_no',
        'supplier_id',
        'restaurant_id',
        'user_id',
        'debit_date',
        'remarks'
    ];
    
    protected $casts = [
        'debit_date' => 'date'
    ];
    
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(DebitNoteItem::class);
    }
    
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
    
    /**
     * Generate debit note number automatically
     */
    public static function generateDebitNoteNo($restaurantId)
    {
        $year = date('Y');
        $month = date('m');
        
        $lastNote = self::where('restaurant_id', $restaurantId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastNote ? intval(substr($lastNote->debit_note_no, -4)) + 1 : 1;
        
        return 'DN-' . $year . $month . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}