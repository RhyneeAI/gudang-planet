<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abs_attendances', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('abs_branch_id')->constrained('abs_branches')->restrictOnDelete();
            $table->foreignId('abs_shift_id')->constrained('abs_shifts')->restrictOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->decimal('check_in_lat', 10, 8)->nullable();
            $table->decimal('check_in_lng', 11, 8)->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->decimal('check_out_lat', 10, 8)->nullable();
            $table->decimal('check_out_lng', 11, 8)->nullable();
            $table->enum('status', [
                'hadir',
                'terlambat',
                'pulang_awal',
                'terlambat_pulang_awal',
                'absen',
            ])->default('hadir');
            $table->text('late_reason')->nullable();
            $table->text('early_reason')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['company_id', 'date']);
            $table->index(['abs_branch_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abs_attendances');
    }
};
