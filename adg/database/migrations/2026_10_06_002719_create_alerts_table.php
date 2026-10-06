<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela alerts (resposta.md §8.11) — Alertas do sistema.
     */
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tipo', 50); // oportunidade, cliente, contrato, lead_inativo, conciliacao
            $table->string('entidade', 100); // opportunities, contracts, conciliactions
            $table->unsignedBigInteger('entidade_id');
            $table->text('mensagem');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('prazo')->nullable();
            $table->boolean('lido')->default(false);
            $table->boolean('resolvido')->default(false);
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();

            $table->index(['entidade', 'entidade_id']);
            $table->index('tipo');
            $table->index('user_id');
            $table->index('lido');
            $table->index('resolvido');
            $table->index('prazo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
