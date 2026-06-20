<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['ops_incomes', 'ops_expenses'] as $tableName) {
            if (Schema::hasColumn($tableName, 'payment_method')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('payment_method', ['TRANSFER', 'CASH'])
                    ->default('CASH')
                    ->after('date');
            });
        }
    }

    public function down(): void
    {
        foreach (['ops_incomes', 'ops_expenses'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'payment_method')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }
};
