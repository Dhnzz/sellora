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
        Schema::create('monthly_revenue_predictions', function (Blueprint $table) {
            $table->id();
            $table->date('prediction_month');
            $table->decimal('predicted_revenue', 15, 4);
            $table->timestamp('prediction_date');
            $table->unique(['prediction_month']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_revenue_predictions');
    }
};
