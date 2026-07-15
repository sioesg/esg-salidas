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
            if (! Schema::hasColumn('folios', 'folio_contpaq')) {
                $table->string('folio_contpaq')->nullable()->after('contpaq_documento_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('folios') || ! Schema::hasColumn('folios', 'folio_contpaq')) {
            return;
        }

        Schema::table('folios', function (Blueprint $table) {
            $table->dropColumn('folio_contpaq');
        });
    }
};
