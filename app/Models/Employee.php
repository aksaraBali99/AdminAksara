<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'address',
        'npwp',
        'ktp_number',
        'ktp_status',
        'start_work_date',
        'position',
        'employee_type',
        'employee_status',
        'base_salary',
        'notes',
    ];

    protected $casts = [
        'start_work_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_RESIGNED = 'resigned';

    const TYPE_FREELANCE = 'freelance';
    const TYPE_FULL_TIME = 'full_time';
    const TYPE_PART_TIME = 'part_time';
    const TYPE_CONTRACT = 'contract';

    const KTP_VERIFIED = 'verified';
    const KTP_PENDING = 'pending';
    const KTP_NOT_SUBMITTED = 'not_submitted';

    const EMPLOYEE_TYPES = [
        self::TYPE_FREELANCE => 'Freelance',
        self::TYPE_FULL_TIME => 'Full Time',
        self::TYPE_PART_TIME => 'Part Time',
        self::TYPE_CONTRACT => 'Contract',
    ];

    const EMPLOYEE_STATUSES = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_TERMINATED => 'Dipecat',
        self::STATUS_RESIGNED => 'Mengundurkan Diri',
    ];

    const KTP_STATUSES = [
        self::KTP_VERIFIED => 'Verified',
        self::KTP_PENDING => 'Pending',
        self::KTP_NOT_SUBMITTED => 'Not Submitted',
    ];

    /**
     * Get the documents for the employee.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * Get the payslips for the employee.
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    /**
     * Get the clients assigned to the employee.
     */
    public function assignedClients(): HasMany
    {
        return $this->hasMany(Client::class, 'assigned_employee_id');
    }

    /**
     * Generate a unique employee ID.
     */
    public static function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $year = Carbon::now()->format('Y');
        
        $lastEmployee = self::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastEmployee ? (intval(substr($lastEmployee->employee_id, -4)) + 1) : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    /**
     * Get formatted base salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        return 'Rp ' . number_format($this->base_salary, 0, ',', '.');
    }

    /**
     * Get employee type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::EMPLOYEE_TYPES[$this->employee_type] ?? $this->employee_type;
    }

    /**
     * Get employee status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::EMPLOYEE_STATUSES[$this->employee_status] ?? $this->employee_status;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->employee_status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_TERMINATED => 'red',
            self::STATUS_RESIGNED => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->employee_id = self::generateEmployeeId();
            }
        });
    }
}
