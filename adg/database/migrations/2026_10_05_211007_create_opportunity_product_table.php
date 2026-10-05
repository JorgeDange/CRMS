<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot opportunity_product (resposta.md §8.5) — many-to-many
     * Produto ↔ Oportunidades.
     */
    public function up(): void
    {
        Schema::create('opportunity_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['opportunity_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_product');
    }
};
