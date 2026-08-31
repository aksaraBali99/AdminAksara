<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_type',
        'file_name',
        'file_path',
    ];

    const DOCUMENT_TYPES = [
        'offer_letter' => 'Offer Letter',
        'ktp' => 'KTP',
        'npwp' => 'NPWP',
        'bpjs_kes' => 'BPJS Kesehatan',
        'bpjs_tk' => 'BPJS Ketenagakerjaan',
        'contract' => 'Work Contract',
        'other' => 'Other',
    ];

    /**
     * Get the employee that owns the document.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get document type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
