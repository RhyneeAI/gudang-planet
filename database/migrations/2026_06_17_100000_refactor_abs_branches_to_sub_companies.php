<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_companies', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->unsignedInteger('radius_meter')->default(50)->after('longitude');
        });

        if (Schema::hasTable('abs_employee_profiles')) {
            Schema::table('abs_employee_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('abs_employee_profiles', 'jabatan')) {
                    $table->string('jabatan')->nullable()->after('user_id');
                }

                if (!Schema::hasColumn('abs_employee_profiles', 'sub_company_id')) {
                    $table->foreignId('sub_company_id')
                        ->nullable()
                        ->after('jabatan')
                        ->constrained('sub_companies')
                        ->nullOnDelete();
                }
            });

            if (Schema::hasColumn('abs_employee_profiles', 'abs_branch_id')) {
                Schema::table('abs_employee_profiles', function (Blueprint $table) {
                    $table->dropForeign(['abs_branch_id']);
                    $table->dropColumn('abs_branch_id');
                });
            }
        }

        if (Schema::hasTable('abs_attendances') && Schema::hasColumn('abs_attendances', 'abs_branch_id')) {
            Schema::table('abs_attendances', function (Blueprint $table) {
                $table->foreignId('sub_company_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('sub_companies')
                    ->restrictOnDelete();
            });

            Schema::table('abs_attendances', function (Blueprint $table) {
                $table->dropForeign(['abs_branch_id']);
                $table->dropIndex(['abs_branch_id', 'date']);
                $table->dropColumn('abs_branch_id');
            });

            Schema::table('abs_attendances', function (Blueprint $table) {
                $table->index(['sub_company_id', 'date']);
            });
        }

        Schema::dropIfExists('abs_branches');
    }

    public function down(): void
    {
        if (!Schema::hasTable('abs_branches')) {
            Schema::create('abs_branches', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('name');
                $table->text('address')->nullable();
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);
                $table->unsignedInteger('radius_meter')->default(50);
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        Schema::table('sub_companies', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius_meter']);
        });
    }
};
