<?php

namespace Database\Seeders;

use App\Models\EstatusUsuario;
use Illuminate\Database\Seeder;

class EstatusUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Activo', 'Inactivo'] as $nombre) {
            EstatusUsuario::query()->updateOrCreate(
                ['nombre' => $nombre],
                ['nombre' => $nombre]
            );
        }
    }
}
