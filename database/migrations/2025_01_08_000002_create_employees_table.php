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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('npwp')->nullable();
            $table->string('ktp_number')->nullable();
            $table->string('ktp_status')->default('not_submitted'); // verified, pending, not_submitted
            $table->date('start_work_date');
            $table->string('position');
            $table->string('employee_type'); // freelance, full_time, part_time
            $table->string('employee_status')->default('active'); // active, terminated, resigned
            $table->decimal('base_salary', 15, 2)->default(0);
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
        Schema::dropIfExists('employees');
    }
};
