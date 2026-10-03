<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela opportunities (PROMPT_MASTER 8.2) + titulo, probabilidade e
     * data_fecho acrescentados por decisao do utilizador (2026-10-03)
     * para reproduzir os cartoes do kanban e o formulario leads-create.
     */
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('titulo');
            $table->string('nome');
            $table->string('empresa')->nullable();
            $table->string('telefone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('nif', 50)->nullable();
            $table->string('codigo_sap', 50)->nullable();
            $table->string('segmento', 100)->nullable();
            $table->string('localizacao')->nullable();
            $table->string('origem', 100)->nullable();
            $table->text('interesse')->nullable();
            $table->decimal('valor_estimado', 15, 2)->nullable();
            $table->unsignedTinyInteger('probabilidade')->nullable();
            $table->date('data_fecho')->nullable();
            $table->text('notas')->nullable();
            $table->string('fase_atual', 50)->default('lead');
            $table->text('motivo_nao_aprovacao')->nullable();
            $table->timestamp('data_aprovacao')->nullable();
            $table->timestamp('ultima_actividade_em')->nullable();
            $table->timestamp('ultimo_contacto_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fase_atual');
            $table->index('email');
            $table->index('telefone');
            $table->index('ultima_actividade_em');
            $table->index('ultimo_contacto_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
