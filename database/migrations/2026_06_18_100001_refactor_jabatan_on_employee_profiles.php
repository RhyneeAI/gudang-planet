<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('abs_employee_profiles')) {
            return;
        }

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('abs_employee_profiles', 'abs_jabatan_id')) {
                $table->foreignId('abs_jabatan_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('abs_jabatans')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('abs_employee_profiles', 'jabatan')) {
            Schema::table('abs_employee_profiles', function (Blueprint $table) {
                $table->dropColumn('jabatan');
            });
        }

        if (Schema::hasColumn('abs_employee_profiles', 'daily_rate')) {
            Schema::table('abs_employee_profiles', function (Blueprint $table) {
                $table->dropColumn('daily_rate');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('abs_employee_profiles')) {
            return;
        }

        Schema::table('abs_employee_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('abs_employee_profiles', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('abs_employee_profiles', 'daily_rate')) {
                $table->decimal('daily_rate', 12, 2)->default(0)->after('abs_shift_id');
            }
        });

        if (Schema::hasColumn('abs_employee_profiles', 'abs_jabatan_id')) {
            Schema::table('abs_employee_profiles', function (Blueprint $table) {
                $table->dropForeign(['abs_jabatan_id']);
                $table->dropColumn('abs_jabatan_id');
            });
        }
    }
};
