<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ops_expenses', function (Blueprint $table) {
            $table->foreignId('transfer_income_id')
                ->nullable()
                ->after('sub_company_id')
                ->constrained('ops_incomes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ops_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transfer_income_id');
        });
    }
};
