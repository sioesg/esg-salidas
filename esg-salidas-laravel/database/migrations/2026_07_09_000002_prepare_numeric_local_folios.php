<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('configuracion') && DB::table('configuracion')->count() === 0) {
            DB::table('configuracion')->insert([
                'folio_prefijo' => '',
                'ultimo_folio' => 0,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (
            DB::getDriverName() === 'mysql'
            && Schema::hasTable('folios')
            && Schema::hasColumn('folios', 'folio')
            && ! $this->hasIndex('folios', 'folios_folio_unique')
            && ! $this->hasDuplicateFolios()
        ) {
            DB::statement('alter table `folios` add unique `folios_folio_unique` (`folio`)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && $this->hasIndex('folios', 'folios_folio_unique')) {
            DB::statement('alter table `folios` drop index `folios_folio_unique`');
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        return DB::selectOne(
            'select 1
             from information_schema.statistics
             where table_schema = database()
               and table_name = ?
               and index_name = ?',
            [$table, $index],
        ) !== null;
    }

    private function hasDuplicateFolios(): bool
    {
        return DB::table('folios')
            ->select('folio')
            ->groupBy('folio')
            ->havingRaw('count(*) > 1')
            ->exists();
    }
};
