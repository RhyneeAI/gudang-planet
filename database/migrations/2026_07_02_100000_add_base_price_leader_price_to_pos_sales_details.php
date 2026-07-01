<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_sales_details', function (Blueprint $table) {
            $table->decimal('base_price', 15, 2)->default(0)->after('product_id');
            $table->decimal('leader_price', 15, 2)->default(0)->after('base_price');
        });
    }

    public function down(): void
    {
        Schema::table('pos_sales_details', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'leader_price']);
        });
    }
};
