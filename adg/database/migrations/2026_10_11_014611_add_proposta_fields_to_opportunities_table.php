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
        Schema::table('opportunities', function (Blueprint $table) {
            // BLOCO 6 (mudancas.md): campos de proposta — uma por oportunidade.
            $table->foreignId('proposta_produto_id')->nullable()->after('produto_id')
                ->constrained('products')->nullOnDelete();
            $table->decimal('proposta_preco', 15, 2)->nullable()->after('proposta_produto_id');
            $table->decimal('proposta_volume', 15, 2)->nullable()->after('proposta_preco');
            $table->string('proposta_volume_unidade', 10)->nullable()->after('proposta_volume');
            $table->date('proposta_periodo_inicio')->nullable()->after('proposta_volume_unidade');
            $table->date('proposta_periodo_fim')->nullable()->after('proposta_periodo_inicio');
            $table->decimal('proposta_credito', 15, 2)->nullable()->after('proposta_periodo_fim');
            $table->decimal('proposta_facturacao', 15, 2)->nullable()->after('proposta_credito');
            $table->decimal('proposta_non_oil', 15, 2)->nullable()->after('proposta_facturacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proposta_produto_id');
            $table->dropColumn([
                'proposta_preco',
                'proposta_volume',
                'proposta_volume_unidade',
                'proposta_periodo_inicio',
                'proposta_periodo_fim',
                'proposta_credito',
                'proposta_facturacao',
                'proposta_non_oil',
            ]);
        });
    }
};
