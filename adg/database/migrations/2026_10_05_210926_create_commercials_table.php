<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela commercials (resposta.md §8.2) — categorias da carteira,
     * não pessoas. Seeder inicial: Comercial 01, 02, 03.
     */
    public function up(): void
    {
        Schema::create('commercials', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercials');
    }
};
