<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'expense_id',
        'payslip_number',
        'period_start',
        'period_end',
        'base_salary',
        'allowances',
        'deductions',
        'pph21',
        'bpjs_kes',
        'bpjs_tk',
        'net_salary',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payment_date' => 'date',
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'pph21' => 'decimal:2',
        'bpjs_kes' => 'decimal:2',
        'bpjs_tk' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the payslip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the expense linked to this payslip.
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get the items for the payslip.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PayslipItem::class)->orderBy('sort_order');
    }

    /**
     * Generate a unique payslip number.
     */
    public static function generatePayslipNumber(): string
    {
        $prefix = 'PAY';
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        $lastPayslip = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPayslip ? (intval(substr($lastPayslip->payslip_number, -4)) + 1) : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    /**
     * Get formatted net salary.
     */
    public function getFormattedNetSalaryAttribute(): string
    {
        return 'Rp ' . number_format($this->net_salary, 0, ',', '.');
    }

    /**
     * Get period label.
     */
    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start->format('d M') . ' - ' . $this->period_end->format('d M Y');
    }

    /**
     * Calculate net salary based on components.
     */
    public function calculateNetSalary(): float
    {
        $gross = $this->base_salary + $this->allowances;
        $totalDeductions = $this->deductions + $this->pph21 + $this->bpjs_kes + $this->bpjs_tk;
        
        return $gross - $totalDeductions;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payslip) {
            if (empty($payslip->payslip_number)) {
                $payslip->payslip_number = self::generatePayslipNumber();
            }
            
            // Auto-calculate net salary if not set
            if (empty($payslip->net_salary)) {
                $payslip->net_salary = $payslip->calculateNetSalary();
            }
        });
    }
}
