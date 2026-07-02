<?php

use App\Models\PosProduct;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_product_price_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('pos_products')->cascadeOnDelete();
            $table->decimal('base_price_old', 15, 2)->default(0);
            $table->decimal('base_price_new', 15, 2)->default(0);
            $table->decimal('leader_price_old', 15, 2)->default(0);
            $table->decimal('leader_price_new', 15, 2)->default(0);
            $table->decimal('marketing_price_old', 15, 2)->default(0);
            $table->decimal('marketing_price_new', 15, 2)->default(0);
            $table->decimal('sell_price_old', 15, 2)->default(0);
            $table->decimal('sell_price_new', 15, 2)->default(0);
            $table->foreignIdFor(User::class, 'changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_product_price_logs');
    }
};
