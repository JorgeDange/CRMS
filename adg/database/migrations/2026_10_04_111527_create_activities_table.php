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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tipo', 50); // visita, chamada, reuniao, proposta, cobranca
            $table->text('descricao');
            $table->text('resultado')->nullable();
            $table->date('data');
            $table->time('hora')->nullable();
            $table->text('proxima_accao')->nullable();
            $table->date('proxima_accao_prazo')->nullable();
            $table->string('status', 50)->default('pendente'); // pendente, concluida, cancelada
            $table->timestamps();

            $table->index(['opportunity_id', 'data']);
            $table->index(['user_id', 'data']);
            $table->index(['status', 'proxima_accao_prazo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
