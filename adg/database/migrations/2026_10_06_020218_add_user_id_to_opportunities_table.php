<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add user_id to opportunities table for dashboard gestor performance.
     */
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('produto_id');
        });
    }

    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
