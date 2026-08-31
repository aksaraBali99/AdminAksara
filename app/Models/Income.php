<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'client_id',
        'source',
        'description',
        'amount',
        'currency',
        'income_date',
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount' => 'decimal:2',
    ];

    const SOURCE_INVOICE = 'invoice_payment';
    const SOURCE_MANUAL = 'manual';

    /**
     * Get the invoice associated with the income.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the client associated with the income.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'AUD' => 'A$',
        ];
        
        $symbol = $symbols[$this->currency] ?? $this->currency;
        
        if ($this->currency === 'IDR') {
            return $symbol . ' ' . number_format($this->amount, 0, ',', '.');
        }
        
        return $symbol . ' ' . number_format($this->amount, 2, '.', ',');
    }
}
