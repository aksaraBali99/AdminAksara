<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'invoice_date',
        'due_date',
        'currency',
        'description',
        'service_type',
        'amount',
        'paid_amount_idr',
        'paid_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount_idr' => 'decimal:2',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';

    const SERVICE_TYPES = [
        'recruiting_fee' => 'Recruiting Fee',
        'va_fee' => 'Virtual Assistant Fee',
        'monthly_benefit' => 'Monthly Benefit',
        'annual_benefit' => 'Annual Benefit',
    ];

    const CURRENCIES = [
        'IDR' => 'IDR',
        'USD' => 'USD',
        'AUD' => 'AUD',
    ];

    /**
     * Get the client that owns the invoice.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the items for the invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        $lastInvoice = self::withTrashed()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, -4)) + 1) : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
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

    /**
     * Get service type label.
     */
    public function getServiceTypeLabelAttribute(): string
    {
        return self::SERVICE_TYPES[$this->service_type] ?? $this->service_type;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SENT => 'blue',
            self::STATUS_PAID => 'green',
            self::STATUS_OVERDUE => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_PAID 
            && $this->due_date->isPast();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });

        // When invoice is created with paid status
        static::created(function ($invoice) {
            if ($invoice->status === self::STATUS_PAID && $invoice->client) {
                // Check if income already exists for this invoice
                $existingIncome = Income::where('invoice_id', $invoice->id)->first();
                if (!$existingIncome) {
                    // Use paid_amount_idr if available, otherwise use invoice amount
                    $incomeAmount = $invoice->paid_amount_idr ?? $invoice->amount;
                    
                    Income::create([
                        'invoice_id' => $invoice->id,
                        'client_id' => $invoice->client_id,
                        'source' => 'invoice_payment',
                        'description' => 'Payment for ' . $invoice->invoice_number,
                        'amount' => $incomeAmount,
                        'currency' => 'IDR', // Always record income in IDR
                        'income_date' => $invoice->paid_date ?? now(),
                    ]);
                }
                
                $invoice->client->updateTotalRevenue();
            }
        });

        // When invoice status is updated to paid or payment details are modified
        static::updated(function ($invoice) {
            // Only process if status is paid
            if ($invoice->status === self::STATUS_PAID && $invoice->client) {
                // Find existing income for this invoice
                $income = Income::where('invoice_id', $invoice->id)->first();
                
                $incomeData = [
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->client_id,
                    'source' => 'invoice_payment',
                    'description' => 'Payment for ' . $invoice->invoice_number,
                    'amount' => $invoice->paid_amount_idr ?? $invoice->amount,
                    'currency' => 'IDR',
                    'income_date' => $invoice->paid_date ?? now(),
                ];

                if ($income) {
                    // Update existing income
                    $income->update($incomeData);
                } else {
                    // Create new income if it doesn't exist
                    Income::create($incomeData);
                }

                $invoice->client->updateTotalRevenue();
            }
        });
    }
}
