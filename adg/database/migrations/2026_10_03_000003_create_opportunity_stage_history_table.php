<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela opportunity_stage_history (PROMPT_MASTER 8.3).
     */
    public function up(): void
    {
        Schema::create('opportunity_stage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->string('fase_anterior', 50)->nullable();
            $table->string('fase_nova', 50);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('observacao')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_stage_history');
    }
};
