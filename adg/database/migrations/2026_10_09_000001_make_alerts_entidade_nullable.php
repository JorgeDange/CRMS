<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table): void {
            // Alertas de sistema (ex.: lembrete da agenda) não têm entidade associada.
            $table->string('entidade', 100)->nullable()->change();
            $table->unsignedBigInteger('entidade_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table): void {
            $table->string('entidade', 100)->nullable(false)->change();
            $table->unsignedBigInteger('entidade_id')->nullable(false)->change();
        });
    }
};
