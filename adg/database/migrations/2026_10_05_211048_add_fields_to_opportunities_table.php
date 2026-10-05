<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos em falta nas opportunities (resposta.md §8.3 + ajustes Sprint 2/3):
     * - pessoa_contacto (nome da pessoa de contacto)
     * - tipo_relacao (enum TipoRelacao)
     * - comercial_id (FK commercials)
     * - produto_id (FK products — single select no create, many-to-many via pivot)
     * - sector (renomeado de segmento)
     * Mantém: codigo_sap, nif, localizacao, origem, interesse, valor_estimado, probabilidade,
     * data_fecho, notas, fase_atual, motivo_nao_aprovacao, data_aprovacao,
     * ultima_actividade_em, ultimo_contacto_em, softDeletes.
     */
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Renomear segmento -> sector (já existe como segmento, vamos renomear)
            if (Schema::hasColumn('opportunities', 'segmento')) {
                $table->renameColumn('segmento', 'sector');
            }

            // Novos campos
            $table->string('pessoa_contacto', 255)->nullable()->after('nome');
            $table->string('tipo_relacao', 50)->nullable()->after('pessoa_contacto');
            $table->foreignId('comercial_id')->nullable()->constrained('commercials')->nullOnDelete()->after('tipo_relacao');
            $table->foreignId('produto_id')->nullable()->constrained('products')->nullOnDelete()->after('comercial_id');

            // Índices para filtros frequentes
            $table->index('comercial_id');
            $table->index('produto_id');
            $table->index('tipo_relacao');
        });
    }

    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropForeign(['comercial_id']);
            $table->dropForeign(['produto_id']);
            $table->dropIndex(['comercial_id']);
            $table->dropIndex(['produto_id']);
            $table->dropIndex(['tipo_relacao']);
            $table->dropColumn(['pessoa_contacto', 'tipo_relacao', 'comercial_id', 'produto_id']);
            // Reverter rename sector -> segmento
            if (Schema::hasColumn('opportunities', 'sector')) {
                $table->renameColumn('sector', 'segmento');
            }
        });
    }
};
