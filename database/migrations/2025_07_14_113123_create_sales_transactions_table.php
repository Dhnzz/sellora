<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
            $table->date('invoice_date');
            $table->decimal('discount_percent', 5, 2)->nullable()->check('discount_percent >= 0 AND discount_percent <= 100');
            $table->decimal('total_amount', 15, 4)->check('total_amount >= 0');
            $table->enum('payment_status', ['pending','paid'])->default('pending');
            $table->timestamp('delivery_confirmed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
    }
};
