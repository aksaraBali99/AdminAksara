<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'contact_name',
        'address',
        'email',
        'phone_number',
        'start_date',
        'total_revenue',
        'assigned_employee_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'total_revenue' => 'decimal:2',
    ];

    /**
     * Get the invoices for the client.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the incomes for the client.
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    /**
     * Get the assigned employee.
     */
    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    /**
     * Update total revenue from paid invoices, converted to IDR.
     *
     * total_revenue is displayed as a plain "Rp" figure (see
     * employees/show.blade.php), so it must be IDR throughout - summing
     * `amount` directly mixes currencies (e.g. IDR 15,000,000 + USD 20 +
     * AUD 5,900 = a meaningless 15,005,920) whenever a client has invoices
     * in more than one currency.
     */
    public function updateTotalRevenue(): void
    {
        $this->total_revenue = $this->invoices()
            ->where('status', 'paid')
            ->get()
            ->sum(fn (Invoice $invoice) => $invoice->paid_amount_idr
                ?? Invoice::convertToIdr((float) $invoice->amount, $invoice->currency));
        $this->save();
    }
}
