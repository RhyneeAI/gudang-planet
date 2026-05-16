<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_installment_plans', function (Blueprint $table) {
            $table->id();
            $table->char('ulid', 26)->unique();
            $table->foreignId('purchase_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->double('total_amount');
            $table->double('paid_amount')->default(0);
            $table->date('start_date');
            $table->enum('status', ['ACTIVE', 'COMPLETED', 'OVERDUE'])->default('ACTIVE');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['purchase_transaction_id', 'company_id']);
            $table->index(['supplier_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_installment_plans');
    }
};