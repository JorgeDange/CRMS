<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela products (resposta.md §8.8) — catálogo de produtos.
     * FK para commercials (responsável pelo produto).
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nome', 255);
            $table->string('numero_serie', 100)->nullable();
            $table->string('codigo', 50)->unique();
            $table->decimal('valor', 15, 2)->nullable();
            $table->foreignId('commercial_id')->nullable()->constrained('commercials')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('commercial_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
