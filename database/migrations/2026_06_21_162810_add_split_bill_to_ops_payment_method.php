<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['ops_incomes', 'ops_expenses'];
    private const VALUES = ['TRANSFER', 'CASH', 'SPLIT_BILL'];

    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        foreach (self::TABLES as $tableName) {
            if ($driver === 'mysql') {
                $enumList = "'" . implode("','", self::VALUES) . "'";
                DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `payment_method` ENUM({$enumList}) DEFAULT 'CASH'");
            } else {
                DB::statement(
                    "ALTER TABLE \"{$tableName}\" DROP CONSTRAINT IF EXISTS \"{$tableName}_payment_method_check\""
                );
                DB::statement(
                    "ALTER TABLE \"{$tableName}\" ADD CONSTRAINT \"{$tableName}_payment_method_check\" "
                    . "CHECK (payment_method::text = ANY (ARRAY['TRANSFER'::character varying, 'CASH'::character varying, 'SPLIT_BILL'::character varying]::text[]))"
                );
            }
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        $oldValues = ['TRANSFER', 'CASH'];

        foreach (self::TABLES as $tableName) {
            if ($driver === 'mysql') {
                $enumList = "'" . implode("','", $oldValues) . "'";
                DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `payment_method` ENUM({$enumList}) DEFAULT 'CASH'");
            } else {
                DB::statement(
                    "ALTER TABLE \"{$tableName}\" DROP CONSTRAINT IF EXISTS \"{$tableName}_payment_method_check\""
                );
                DB::statement(
                    "ALTER TABLE \"{$tableName}\" ADD CONSTRAINT \"{$tableName}_payment_method_check\" "
                    . "CHECK (payment_method::text = ANY (ARRAY['TRANSFER'::character varying, 'CASH'::character varying]::text[]))"
                );
            }
        }
    }
};
