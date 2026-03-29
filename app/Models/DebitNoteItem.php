<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebitNoteItem extends Model
{
    protected $table = 'debit_note_items';
    
    protected $fillable = [
        'debit_note_id',
        'product_id',
        'unit_id',
        'quantity',
        'restaurant_id'
    ];
    
    protected $casts = [
        'quantity' => 'decimal:3'
    ];
    
    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
    
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}