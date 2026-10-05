<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela contracts (resposta.md §8.10) — Contratos/Encomendas.
     * Substitui o antigo "Encomendas" do protótipo.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commercial_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('numero_contrato', 50)->unique();
            $table->string('tipo', 50)->nullable();
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('estado', 50)->default('activo'); // activo, expirado, renovado, cancelado
            $table->string('documento', 255)->nullable();
            $table->text('notas')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('opportunity_id');
            $table->index('commercial_id');
            $table->index('product_id');
            $table->index('estado');
            $table->index('data_fim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
