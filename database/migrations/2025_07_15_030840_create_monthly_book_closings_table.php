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
        Schema::create('monthly_book_closings', function (Blueprint $table) {
            $table->id();
            $table->integer('closing_month'); // 1-12, tipe bulan
            $table->year('closing_year');
            $table->timestamp('closed_at')->useCurrent();
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
            $table->unique(['closing_month', 'closing_year'], 'uniq_month_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_book_closings');
    }
};
