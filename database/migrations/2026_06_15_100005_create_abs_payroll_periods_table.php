<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abs_payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->decimal('daily_rate', 12, 2);
            $table->unsignedInteger('total_days')->default(0);
            $table->decimal('gross_salary', 14, 2)->default(0);
            $table->decimal('total_deduction', 14, 2)->default(0);
            $table->decimal('net_salary', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'period_month', 'period_year']);
            $table->index(['company_id', 'period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abs_payroll_periods');
    }
};
