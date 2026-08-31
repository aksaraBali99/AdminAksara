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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('type_of_fees'); // virtual_assistant_fee, monthly_benefit, annual_benefit
            $table->text('description');
            $table->decimal('item_hours', 10, 2)->default(1);
            $table->string('currency', 10)->default('IDR'); // IDR, USD, AUD
            $table->decimal('rate', 15, 2);
            $table->decimal('total', 15, 2);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
