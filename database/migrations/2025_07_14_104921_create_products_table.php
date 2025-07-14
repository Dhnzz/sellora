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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_brand_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name', 255);
            $table->foreignId('minimum_selling_unit_id')
                ->constrained('product_units')
                ->cascadeOnDelete();
            $table->json('conversion_factors')->nullable();
            $table->decimal('selling_price', 12, 4)->check('selling_price >= 0');
            $table->timestamps();
            $table->unique(['product_brand_id', 'name']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
