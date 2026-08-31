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
        Schema::table('payslips', function (Blueprint $table) {
            if (!Schema::hasColumn('payslips', 'expense_id')) {
                $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            if (Schema::hasColumn('payslips', 'expense_id')) {
                $table->dropForeign(['expense_id']);
                $table->dropColumn('expense_id');
            }
        });
    }
};
