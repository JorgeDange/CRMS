<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela conciliactions (resposta.md §8.9) — Conciliações bancárias.
     * Substitui o antigo "Crédito" do protótipo.
     */
    public function up(): void
    {
        Schema::create('conciliactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('opportunity_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('commercial_id')->nullable()->constrained()->nullOnDelete();
            $table->date('data');
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('classificacao', 50)->default('pendente'); // concordante, discordante, pendente
            $table->text('observacoes')->nullable();
            $table->boolean('resolvido')->default(false);
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();

            $table->index('opportunity_id');
            $table->index('commercial_id');
            $table->index('data');
            $table->index('classificacao');
            $table->index('resolvido');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conciliactions');
    }
};
