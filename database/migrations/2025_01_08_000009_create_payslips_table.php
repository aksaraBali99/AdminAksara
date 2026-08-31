<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('payslip_number')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('base_salary', 15, 2);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('pph21', 15, 2)->default(0);
            $table->decimal('bpjs_kes', 15, 2)->default(0);
            $table->decimal('bpjs_tk', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
