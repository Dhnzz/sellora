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
        Schema::create('product_associations', function (Blueprint $table) {
            $table->id();
            $table->json('antecedent_product_ids');
            $table->json('consequent_product_ids');
            $table->decimal('support', 8, 6);
            $table->decimal('confidence', 8, 6);
            $table->decimal('lift', 8, 6);
            $table->date('analysis_date');
            $table->unique(['atecedent_product_ids', 'consequent_product_ids', 'analysis_date']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_associations');
    }
};
