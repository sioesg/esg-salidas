<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('folios')) {
            Schema::create('folios', function (Blueprint $table) {
                $table->id();
                $table->string('folio')->unique();
                $table->timestamp('fecha')->nullable();
                $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('usuario_nombre')->nullable();
                $table->unsignedBigInteger('departamento_id')->nullable();
                $table->string('departamento_nombre')->nullable();
                $table->unsignedBigInteger('unidad_id')->nullable();
                $table->string('unidad_nombre')->nullable();
                $table->string('tipo_salida')->nullable();
                $table->text('observaciones')->nullable();
                $table->unsignedInteger('total_productos')->default(0);
                $table->string('contpaq_documento_id')->nullable();
                $table->string('estado')->default('Pendiente');
                $table->timestamp('fecha_envio')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('detalle_folios')) {
            Schema::create('detalle_folios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('folio_id')->constrained('folios')->cascadeOnDelete();
                $table->string('producto_id');
                $table->string('codigo_producto');
                $table->string('nombre_producto');
                $table->string('unidad_medida')->nullable();
                $table->decimal('cantidad', 12, 4);
                $table->decimal('existencia_actual', 12, 4)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('logs_sincronizacion')) {
            Schema::create('logs_sincronizacion', function (Blueprint $table) {
                $table->id();
                $table->foreignId('folio_id')->nullable()->constrained('folios')->nullOnDelete();
                $table->unsignedInteger('intento')->default(1);
                $table->string('estatus');
                $table->integer('codigo_respuesta')->nullable();
                $table->text('mensaje')->nullable();
                $table->json('payload')->nullable();
                $table->json('respuesta')->nullable();
                $table->timestamp('fecha_envio')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('configuracion')) {
            Schema::create('configuracion', function (Blueprint $table) {
                $table->id();
                $table->string('nombre_empresa')->nullable();
                $table->string('api_contpaq_url')->nullable();
                $table->text('api_contpaq_token')->nullable();
                $table->unsignedInteger('api_contpaq_timeout')->nullable();
                $table->string('folio_prefijo')->default('SAL');
                $table->unsignedInteger('ultimo_folio')->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
        Schema::dropIfExists('logs_sincronizacion');
        Schema::dropIfExists('detalle_folios');
        Schema::dropIfExists('folios');
    }
};
