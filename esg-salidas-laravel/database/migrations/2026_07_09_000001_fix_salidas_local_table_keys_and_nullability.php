<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach (['folios', 'detalle_folios', 'logs_sincronizacion', 'configuracion'] as $table) {
            $this->ensureAutoIncrementPrimaryKey($table);
        }

        $this->modifyColumnIfExists('folios', 'departamento_id', 'varchar(50) null');
        $this->modifyColumnIfExists('folios', 'unidad_id', 'varchar(50) null');
        $this->modifyColumnIfExists('folios', 'unidad_nombre', 'varchar(100) null');
        $this->modifyColumnIfExists('folios', 'tipo_salida', 'varchar(50) null');
        $this->modifyColumnIfExists('folios', 'observaciones', 'text null');
        $this->modifyColumnIfExists('folios', 'contpaq_documento_id', 'varchar(100) null');
        $this->modifyColumnIfExists('folios', 'estado', "varchar(50) not null default 'Pendiente'");
        $this->modifyColumnIfExists('folios', 'fecha_envio', 'datetime null');

        $this->modifyColumnIfExists('detalle_folios', 'unidad_medida', 'varchar(30) null');
        $this->modifyColumnIfExists('detalle_folios', 'existencia_actual', 'decimal(10,2) null');

        $this->modifyColumnIfExists('logs_sincronizacion', 'folio_id', 'bigint null');
        $this->modifyColumnIfExists('logs_sincronizacion', 'estatus', 'varchar(50) not null');
        $this->modifyColumnIfExists('logs_sincronizacion', 'codigo_respuesta', 'varchar(20) null');
        $this->modifyColumnIfExists('logs_sincronizacion', 'mensaje', 'text null');
        $this->modifyColumnIfExists('logs_sincronizacion', 'payload', 'longtext null');
        $this->modifyColumnIfExists('logs_sincronizacion', 'respuesta', 'longtext null');
        $this->modifyColumnIfExists('logs_sincronizacion', 'fecha_envio', 'datetime null');

        $this->modifyColumnIfExists('configuracion', 'nombre_empresa', 'varchar(150) null');
        $this->modifyColumnIfExists('configuracion', 'api_contpaq_url', 'varchar(255) null');
        $this->modifyColumnIfExists('configuracion', 'api_contpaq_token', 'text null');
        $this->modifyColumnIfExists('configuracion', 'api_contpaq_timeout', 'int null');
        $this->modifyColumnIfExists('configuracion', 'folio_prefijo', "varchar(20) not null default 'SAL'");
        $this->modifyColumnIfExists('configuracion', 'ultimo_folio', 'int not null default 0');
        $this->modifyColumnIfExists('configuracion', 'activo', 'tinyint(1) not null default 1');

        $this->ensureForeignKey(
            'detalle_folios',
            'detalle_folios_folio_id_foreign',
            'folio_id',
            'folios',
            'id',
            'cascade'
        );

        $this->ensureForeignKey(
            'logs_sincronizacion',
            'logs_sincronizacion_folio_id_foreign',
            'folio_id',
            'folios',
            'id',
            'set null'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->dropForeignKeyIfExists('logs_sincronizacion', 'logs_sincronizacion_folio_id_foreign');
        $this->dropForeignKeyIfExists('detalle_folios', 'detalle_folios_folio_id_foreign');
    }

    private function ensureAutoIncrementPrimaryKey(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
            return;
        }

        $column = DB::selectOne(
            'select column_type, extra
             from information_schema.columns
             where table_schema = database()
               and table_name = ?
               and column_name = ?',
            [$table, 'id'],
        );

        if (! $column) {
            return;
        }

        $type = str_contains(strtolower($column->column_type), 'unsigned')
            ? 'bigint unsigned'
            : 'bigint';

        if ($this->hasPrimaryKey($table)) {
            if (! str_contains(strtolower($column->extra), 'auto_increment')) {
                DB::statement("alter table `{$table}` modify `id` {$type} not null auto_increment");
            }

            return;
        }

        DB::statement("alter table `{$table}` modify `id` {$type} not null auto_increment, add primary key (`id`)");
    }

    private function modifyColumnIfExists(string $table, string $column, string $definition): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        DB::statement("alter table `{$table}` modify `{$column}` {$definition}");
    }

    private function hasPrimaryKey(string $table): bool
    {
        return DB::selectOne(
            'select 1
             from information_schema.table_constraints
             where table_schema = database()
               and table_name = ?
               and constraint_type = ?',
            [$table, 'PRIMARY KEY'],
        ) !== null;
    }

    private function ensureForeignKey(
        string $table,
        string $constraint,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete,
    ): void {
        if (! Schema::hasTable($table) || ! Schema::hasTable($referencedTable)) {
            return;
        }

        if ($this->hasForeignKey($table, $constraint)) {
            return;
        }

        DB::statement(
            "alter table `{$table}` add constraint `{$constraint}` foreign key (`{$column}`) references `{$referencedTable}` (`{$referencedColumn}`) on delete {$onDelete}"
        );
    }

    private function dropForeignKeyIfExists(string $table, string $constraint): void
    {
        if (! Schema::hasTable($table) || ! $this->hasForeignKey($table, $constraint)) {
            return;
        }

        DB::statement("alter table `{$table}` drop foreign key `{$constraint}`");
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        return DB::selectOne(
            'select 1
             from information_schema.table_constraints
             where table_schema = database()
               and table_name = ?
               and constraint_name = ?
               and constraint_type = ?',
            [$table, $constraint, 'FOREIGN KEY'],
        ) !== null;
    }
};
