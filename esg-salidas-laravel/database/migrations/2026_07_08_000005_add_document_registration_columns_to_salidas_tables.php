<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('folios') || ! Schema::hasTable('detalle_folios')) {
            return;
        }

        Schema::table('folios', function (Blueprint $table) {
            if (! Schema::hasColumn('folios', 'departamento_codigo')) {
                $table->string('departamento_codigo')->nullable()->after('departamento_id');
            }

            if (! Schema::hasColumn('folios', 'referencia')) {
                $table->string('referencia')->nullable()->after('unidad_nombre');
            }

            if (! Schema::hasColumn('folios', 'codigo_almacen')) {
                $table->string('codigo_almacen')->nullable()->after('total_productos');
            }
        });

        Schema::table('detalle_folios', function (Blueprint $table) {
            if (! Schema::hasColumn('detalle_folios', 'unidad_id')) {
                $table->unsignedBigInteger('unidad_id')->nullable()->after('nombre_producto');
            }

            if (! Schema::hasColumn('detalle_folios', 'precio')) {
                $table->decimal('precio', 12, 2)->default(0)->after('cantidad');
            }

            if (! Schema::hasColumn('detalle_folios', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('existencia_actual');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('folios') || ! Schema::hasTable('detalle_folios')) {
            return;
        }

        Schema::table('detalle_folios', function (Blueprint $table) {
            foreach (['observaciones', 'precio', 'unidad_id'] as $column) {
                if (Schema::hasColumn('detalle_folios', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('folios', function (Blueprint $table) {
            foreach (['codigo_almacen', 'referencia', 'departamento_codigo'] as $column) {
                if (Schema::hasColumn('folios', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
