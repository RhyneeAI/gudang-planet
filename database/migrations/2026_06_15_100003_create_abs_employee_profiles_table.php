<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abs_employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('abs_branch_id')->constrained('abs_branches')->restrictOnDelete();
            $table->foreignId('abs_shift_id')->nullable()->constrained('abs_shifts')->nullOnDelete();
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'abs_branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abs_employee_profiles');
    }
};
