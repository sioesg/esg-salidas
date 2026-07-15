<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')
                    ->nullable()
                    ->after('password')
                    ->constrained('roles')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'estatus_id')) {
                $table->foreignId('estatus_id')
                    ->nullable()
                    ->after('role_id')
                    ->constrained('estatus_usuario')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'contpaq_usuario_id')) {
                $table->string('contpaq_usuario_id')
                    ->nullable()
                    ->after('estatus_id');
            }

            if (! Schema::hasColumn('users', 'ultimo_acceso')) {
                $table->timestamp('ultimo_acceso')
                    ->nullable()
                    ->after('remember_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'contpaq_usuario_id')) {
                $table->dropColumn('contpaq_usuario_id');
            }

            if (Schema::hasColumn('users', 'ultimo_acceso')) {
                $table->dropColumn('ultimo_acceso');
            }

            if (Schema::hasColumn('users', 'estatus_id')) {
                $table->dropConstrainedForeignId('estatus_id');
            }

            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
        });
    }
};
