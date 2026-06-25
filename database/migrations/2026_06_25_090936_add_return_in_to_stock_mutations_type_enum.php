<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pos_stock_mutations MODIFY COLUMN type ENUM('PURCHASE_IN', 'SALES_OUT', 'RETURN_IN', 'ADJUST_IN', 'ADJUST_OUT', 'OPNAME')");
        } else {
            DB::statement("ALTER TABLE pos_stock_mutations DROP CONSTRAINT pos_stock_mutations_type_check");
            DB::statement("ALTER TABLE pos_stock_mutations ADD CONSTRAINT pos_stock_mutations_type_check CHECK (type IN ('PURCHASE_IN', 'SALES_OUT', 'RETURN_IN', 'ADJUST_IN', 'ADJUST_OUT', 'OPNAME'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pos_stock_mutations MODIFY COLUMN type ENUM('PURCHASE_IN', 'SALES_OUT', 'ADJUST_IN', 'ADJUST_OUT', 'OPNAME')");
        } else {
            DB::statement("ALTER TABLE pos_stock_mutations DROP CONSTRAINT pos_stock_mutations_type_check");
            DB::statement("ALTER TABLE pos_stock_mutations ADD CONSTRAINT pos_stock_mutations_type_check CHECK (type IN ('PURCHASE_IN', 'SALES_OUT', 'ADJUST_IN', 'ADJUST_OUT', 'OPNAME'))");
        }
    }
};
