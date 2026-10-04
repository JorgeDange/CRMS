<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela visit_reports (PROMPT_MASTER 8.5).
     */
    public function up(): void
    {
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('data_visita');
            $table->text('objectivo')->nullable();
            $table->text('pessoas_presentes')->nullable();
            $table->text('assuntos_tratados');
            $table->text('resultado')->nullable();
            $table->text('proxima_accao')->nullable();
            $table->json('anexos')->nullable();
            $table->timestamps();

            $table->index('data_visita');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_reports');
    }
};
