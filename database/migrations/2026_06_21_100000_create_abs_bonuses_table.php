<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abs_bonuses', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('abs_payroll_period_id')->constrained('abs_payroll_periods')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->decimal('amount', 12, 2);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['abs_payroll_period_id']);
            $table->index(['user_id']);
        });

        Schema::table('abs_payroll_periods', function (Blueprint $table) {
            $table->decimal('total_bonus', 14, 2)->default(0)->after('total_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('abs_payroll_periods', function (Blueprint $table) {
            $table->dropColumn('total_bonus');
        });

        Schema::dropIfExists('abs_bonuses');
    }
};
