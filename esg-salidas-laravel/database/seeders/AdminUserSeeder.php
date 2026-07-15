<?php

namespace Database\Seeders;

use App\Models\EstatusUsuario;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()->where('nombre', 'Admin')->firstOrFail();
        $estatus = EstatusUsuario::query()->where('nombre', 'Activo')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role_id' => $role->id,
                'estatus_id' => $estatus->id,
            ]
        );
    }
}
