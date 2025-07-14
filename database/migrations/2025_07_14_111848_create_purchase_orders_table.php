<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cusomter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_agent_id')->constrained()->cascadeOnDelete();
            $table->date('order_date');
            $table->date('delivery_date');
            $table->enum('status', ['pending', 'confirmed', 'delivered', 'returned'])->default('pending');
            $table->decimal('discount_percent', 5, 2)->nullable()->check('discount_percent >= 0 AND discount_percent <= 100');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
