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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('currency')->default('IDR'); // IDR, USD, AUD
            $table->text('description')->nullable();
            $table->string('service_type'); // recruiting_fee, va_fee, monthly_benefit, annual_benefit
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('draft'); // draft, sent, paid, overdue
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
