<?php

namespace Tests\Feature;

use App\Models\EstatusUsuario;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private Role $role;

    private EstatusUsuario $activo;

    private EstatusUsuario $inactivo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::query()->create(['nombre' => 'Admin']);
        $this->activo = EstatusUsuario::query()->create(['nombre' => 'Activo']);
        $this->inactivo = EstatusUsuario::query()->create(['nombre' => 'Inactivo']);
    }

    public function test_active_admin_can_login(): void
    {
        $user = $this->createUser($this->activo);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role' => ['id', 'nombre'],
                    'estatus' => ['id', 'nombre'],
                ],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.role.nombre', 'Admin')
            ->assertJsonPath('user.estatus.nombre', 'Activo');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->createUser($this->activo);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    public function test_inactive_user_is_rejected(): void
    {
        $user = $this->createUser($this->inactivo);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])->assertForbidden();
    }

    public function test_me_requires_token(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_me_returns_authenticated_user_with_valid_token(): void
    {
        $user = $this->createUser($this->activo);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = $this->createUser($this->activo);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        $this->app['auth']->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertUnauthorized();
    }

    public function test_products_are_protected(): void
    {
        $this->getJson('/api/productos')->assertUnauthorized();
    }

    private function createUser(EstatusUsuario $estatus): User
    {
        return User::query()->create([
            'name' => 'Admin Test',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('secret'),
            'role_id' => $this->role->id,
            'estatus_id' => $estatus->id,
        ]);
    }
}
