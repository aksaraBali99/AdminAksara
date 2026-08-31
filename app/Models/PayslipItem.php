<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayslipItem extends Model
{
    protected $fillable = [
        'payslip_id',
        'description',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the payslip that owns the item.
     */
    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
