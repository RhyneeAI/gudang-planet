<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('abs_employee_profiles') || !Schema::hasColumn('abs_employee_profiles', 'abs_shift_id')) {
            return;
        }

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            $table->dropForeign(['abs_shift_id']);
        });

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('abs_shift_id')->nullable()->change();
        });

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            $table->foreign('abs_shift_id')
                ->references('id')
                ->on('abs_shifts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('abs_employee_profiles') || !Schema::hasColumn('abs_employee_profiles', 'abs_shift_id')) {
            return;
        }

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            $table->dropForeign(['abs_shift_id']);
        });

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('abs_shift_id')->nullable(false)->change();
        });

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            $table->foreign('abs_shift_id')
                ->references('id')
                ->on('abs_shifts')
                ->restrictOnDelete();
        });
    }
};
