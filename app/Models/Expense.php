<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'description',
        'amount',
        'currency',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the category of the expense.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
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
