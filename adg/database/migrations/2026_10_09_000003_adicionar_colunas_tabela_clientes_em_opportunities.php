<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 13 — Tabela de Clientes: campos denormalizados em opportunities
 * para a tabela /clientes sem joins (seguir.md §3).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table): void {
            $table->string('codigo', 50)->nullable()->after('codigo_sap');
            $table->boolean('tem_compras')->default(false)->after('codigo');
            $table->boolean('assinatura_director')->default(false)->after('tem_compras');
            $table->boolean('enviado_entregue')->default(false)->after('assinatura_director');
            $table->string('conciliacao_estado', 50)->nullable()->after('enviado_entregue');
            $table->string('contrato_estado', 50)->nullable()->after('conciliacao_estado');

            $table->index('codigo');
            $table->index('tem_compras');
            $table->index('contrato_estado');
            $table->index('conciliacao_estado');
        });
    }

    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table): void {
            $table->dropIndex(['codigo']);
            $table->dropIndex(['tem_compras']);
            $table->dropIndex(['contrato_estado']);
            $table->dropIndex(['conciliacao_estado']);

            $table->dropColumn([
                'codigo',
                'tem_compras',
                'assinatura_director',
                'enviado_entregue',
                'conciliacao_estado',
                'contrato_estado',
            ]);
        });
    }
};
