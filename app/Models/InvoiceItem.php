<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'type_of_fees',
        'description',
        'item_hours',
        'currency',
        'rate',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'item_hours' => 'decimal:2',
        'rate' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    const TYPE_OF_FEES = [
        'virtual_assistant_fee' => 'Virtual Assistant Fee',
        'monthly_benefit' => 'Monthly Benefit',
        'annual_benefit' => 'Annual Benefit',
    ];

    /**
     * Get the invoice that owns the item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get formatted total with currency.
     */
    public function getFormattedTotalAttribute(): string
    {
        $symbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'AUD' => 'A$',
        ];
        
        $symbol = $symbols[$this->currency] ?? $this->currency;
        
        if ($this->currency === 'IDR') {
            return $symbol . ' ' . number_format($this->total, 0, ',', '.');
        }
        
        return $symbol . ' ' . number_format($this->total, 2, '.', ',');
    }
}
