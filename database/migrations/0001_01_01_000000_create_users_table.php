<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            // $table->string('username')->unique(); // ← HAPUS atau COMMENT
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->unique()->nullable(); // ← TAMBAHKAN unique
            $table->string('password');
            $table->enum('role', ['SUPERADMIN', 'OWNER', 'ADMIN', 'HRD', 'GUDANG', 'KEPALA_GUDANG', 'MARKETING_LEAD', 'MARKETING', 'MARKETING_TETAP', 'KASIR', 'KEPALA_MANDOR', 'MANDOR', 'KARYAWAN']);
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['email', 'company_id']); 
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};