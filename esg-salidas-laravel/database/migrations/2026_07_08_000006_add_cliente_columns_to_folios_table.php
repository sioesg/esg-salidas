<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('folios')) {
            return;
        }

        Schema::table('folios', function (Blueprint $table) {
            if (! Schema::hasColumn('folios', 'cliente_codigo')) {
                $table->string('cliente_codigo')->nullable()->after('departamento_nombre');
            }

            if (! Schema::hasColumn('folios', 'cliente_nombre')) {
                $table->string('cliente_nombre')->nullable()->after('cliente_codigo');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('folios')) {
            return;
        }

        Schema::table('folios', function (Blueprint $table) {
            foreach (['cliente_nombre', 'cliente_codigo'] as $column) {
                if (Schema::hasColumn('folios', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
