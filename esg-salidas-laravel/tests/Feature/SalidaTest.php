<?php

namespace Tests\Feature;

use App\Models\EstatusUsuario;
use App\Models\Folio;
use App\Models\Role;
use App\Models\User;
use App\Services\Salidas\SalidaService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SalidaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $role = Role::query()->create(['nombre' => 'Admin']);
        $estatus = EstatusUsuario::query()->create(['nombre' => 'Activo']);

        $this->user = User::query()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret'),
            'role_id' => $role->id,
            'estatus_id' => $estatus->id,
        ]);
    }

    public function test_can_register_salida_and_send_expected_payload(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response([
                'ok' => true,
                'idDocumento' => 9527120,
                'folio' => '211857',
            ], 200),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertCreated()
            ->assertJsonPath('message', 'Salida registrada correctamente.')
            ->assertJsonPath('data.estado', 'Sincronizado')
            ->assertJsonPath('data.folio_contpaq', '211857')
            ->assertJsonPath('data.contpaq_documento_id', '9527120');

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $payload['concepto'] === '102'
                && $payload['serie'] === 'CONSUMOS'
                && $payload['codigoClienteProveedor'] === '000000'
                && $payload['referencia'] === ''
                && $payload['movimientos'][0]['codProd'] === 'Z0001'
                && ! array_key_exists('unidad', $payload['movimientos'][0])
                && $payload['movimientos'][0]['precio'] === 0.00
                && $payload['movimientos'][0]['codAlmacen'] === '1';
        });

        $this->assertDatabaseHas('folios', [
            'folio_contpaq' => '211857',
            'contpaq_documento_id' => '9527120',
        ]);
    }

    public function test_local_folios_are_numeric_and_incremental(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response([
                'ok' => true,
                'idDocumento' => 9527120,
                'folio' => '211857',
            ], 200),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.folio', '0001');

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.folio', '0002');

        $this->assertDatabaseHas('configuracion', [
            'ultimo_folio' => 2,
        ]);
    }

    public function test_register_salida_with_vehicle_unit_adds_unit_to_movements(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response([
                'ok' => true,
                'idDocumento' => 9527121,
                'folio' => '211858',
            ], 200),
        ]);

        $payload = $this->payload(['unidad_vehicular' => 109]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $payload)
            ->assertCreated();

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $payload['movimientos'][0]['unidad'] === 109
                && $payload['movimientos'][0]['codProd'] === 'Z0001';
        });
    }

    public function test_register_salida_with_multiple_products_without_vehicle_unit_omits_unit(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response([
                'ok' => true,
                'idDocumento' => 9527122,
                'folio' => '211859',
            ], 200),
        ]);

        $payload = $this->payload([
            'productos' => [
                [
                    'id' => 583,
                    'codigo' => 'Z0001',
                    'nombre' => 'FLETE',
                    'cantidad' => 2,
                    'existencia' => 865,
                ],
                [
                    'id' => 584,
                    'codigo' => 'Z0002',
                    'nombre' => 'PRODUCTO DOS',
                    'cantidad' => 3,
                    'existencia' => 10,
                ],
            ],
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $payload)
            ->assertCreated()
            ->assertJsonPath('data.total_productos', 2);

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return count($payload['movimientos']) === 2
                && ! array_key_exists('unidad', $payload['movimientos'][0])
                && ! array_key_exists('unidad', $payload['movimientos'][1])
                && $payload['movimientos'][0]['codProd'] === 'Z0001'
                && $payload['movimientos'][1]['codProd'] === 'Z0002';
        });
    }

    public function test_departamentos_endpoint_returns_internal_clients_from_contpaq(): void
    {
        config([
            'services.contpaq.clientes_proveedores_url' => 'http://contpaq.test/clientes-proveedores',
            'services.contpaq.clientes_internos_codigos' => '000000,5703',
        ]);

        Http::fake([
            'http://contpaq.test/clientes-proveedores' => Http::response([
                'ok' => true,
                'total' => 3,
                'clientes' => [
                    [
                        'cidclienteproveedor' => 1695,
                        'ccodigocliente' => '000000',
                        'crazonsocial' => 'CLIENTEPRUBA',
                    ],
                    [
                        'cidclienteproveedor' => 14200,
                        'ccodigocliente' => '5703',
                        'crazonsocial' => 'Jefatura Almacen',
                    ],
                    [
                        'cidclienteproveedor' => 1046,
                        'ccodigocliente' => '240',
                        'crazonsocial' => 'COSTCO DE MEXICO',
                    ],
                ],
            ], 200),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/departamentos')
            ->assertOk()
            ->assertJsonPath('data.0.codigo', '000000')
            ->assertJsonPath('data.0.nombre', 'CLIENTEPRUBA')
            ->assertJsonPath('data.1.codigo', '5703')
            ->assertJsonPath('data.1.nombre', 'Jefatura Almacen')
            ->assertJsonCount(2, 'data');
    }

    public function test_unidades_endpoint_returns_vehicle_units_from_contpaq(): void
    {
        config(['services.contpaq.unidades_url' => 'http://contpaq.test/unidades']);

        Http::fake([
            'http://contpaq.test/unidades' => Http::response([
                [
                    'idunidad' => 0,
                    'numeroEconomico' => 0,
                    'tipo' => '',
                ],
                [
                    'idunidad' => 16,
                    'numeroEconomico' => 34,
                    'tipo' => 'GUZZLER',
                    'marca' => 'INTERNATIONAL',
                    'modelo' => '1999',
                    'placas' => 'RB6995B',
                ],
            ], 200),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/unidades')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 34)
            ->assertJsonPath('data.0.numero', '34')
            ->assertJsonPath('data.0.nombre', 'Unidad 34 - GUZZLER')
            ->assertJsonPath('data.0.contpaq_id', 16);
    }

    public function test_external_failure_keeps_local_salida_with_error_status(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response(['message' => 'Error externo'], 500),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertStatus(502)
            ->assertJsonPath('upstream_status', 500)
            ->assertJsonPath('upstream_message', 'Error externo')
            ->assertJsonPath('error_type', 'upstream_http_error');

        $this->assertDatabaseHas('folios', [
            'cliente_codigo' => '000000',
            'usuario_nombre' => 'Juan Perez',
            'usuario_id' => $this->user->id,
            'estado' => 'Error',
        ]);

        $this->assertDatabaseHas('logs_sincronizacion', [
            'estatus' => 'Error',
            'codigo_respuesta' => 500,
        ]);

        Http::assertSentCount(1);
    }

    public function test_timeout_returns_timeout_error_and_logs_diagnostics(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => fn () => throw new ConnectionException(
                'cURL error 28: Operation timed out after 60000 milliseconds with 0 bytes received'
            ),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertStatus(502)
            ->assertJsonPath('message', 'La salida se guardo localmente, pero CONTPAQi no respondio dentro del tiempo esperado.')
            ->assertJsonPath('upstream_status', null)
            ->assertJsonPath('error_type', 'timeout_error');

        $log = \App\Models\LogSincronizacion::query()->latest('id')->firstOrFail();

        $this->assertSame('Error', $log->estatus);
        $this->assertSame('timeout_error', $log->respuesta['error_type']);
        $this->assertSame(config('services.contpaq.documentos_facturas_url'), $log->respuesta['url']);
        $this->assertArrayHasKey('duration_ms', $log->respuesta);
        $this->assertArrayNotHasKey('unidad', $log->payload['movimientos'][0]);
    }

    public function test_external_400_returns_diagnostic_and_logs_response(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response(['message' => 'Producto invalido'], 400),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertStatus(502)
            ->assertJsonPath('upstream_status', 400)
            ->assertJsonPath('upstream_message', 'Producto invalido')
            ->assertJsonPath('error_type', 'upstream_http_error');

        $this->assertDatabaseHas('logs_sincronizacion', [
            'estatus' => 'Error',
            'codigo_respuesta' => 400,
            'mensaje' => 'Producto invalido',
        ]);
    }

    public function test_connection_failure_keeps_local_salida_and_returns_diagnostic(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::failedConnection(),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertStatus(502)
            ->assertJsonPath('upstream_status', null)
            ->assertJsonPath('error_type', 'connection_error');

        $this->assertDatabaseHas('folios', [
            'cliente_codigo' => '000000',
            'estado' => 'Error',
        ]);

        $this->assertDatabaseHas('logs_sincronizacion', [
            'estatus' => 'Error',
            'codigo_respuesta' => null,
        ]);
    }

    public function test_database_error_returns_local_persistence_message(): void
    {
        $this->mock(SalidaService::class, function ($mock) {
            $mock->shouldReceive('registrar')
                ->once()
                ->andThrow(new QueryException(
                    'mysql',
                    'insert into folios (...) values (...)',
                    [],
                    new Exception("SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value")
                ));
        });

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload())
            ->assertStatus(500)
            ->assertJsonPath('message', 'No fue posible guardar la salida localmente.')
            ->assertJsonPath('error_type', 'database_error');
    }

    public function test_historial_detalle_and_reportes_use_real_database(): void
    {
        Http::fake([
            config('services.contpaq.documentos_facturas_url') => Http::response([
                'ok' => true,
                'idDocumento' => 9527120,
                'folio' => '211857',
            ], 200),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload(['referencia' => 'UNIDAD 15']))
            ->assertCreated();

        $folio = Folio::query()->latest('id')->firstOrFail();

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/salidas')
            ->assertOk()
            ->assertJsonPath('data.0.folio', $folio->folio);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/salidas/' . $folio->folio)
            ->assertOk()
            ->assertJsonPath('data.productos.0.codigo', 'Z0001')
            ->assertJsonPath('data.usuario_nombre', 'Juan Perez')
            ->assertJsonPath('data.cliente_codigo', '000000')
            ->assertJsonPath('data.referencia', 'UNIDAD 15');

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/reportes/resumen')
            ->assertOk()
            ->assertJsonPath('data.salidas_totales', 1)
            ->assertJsonPath('data.salidas_sincronizadas', 1);
    }

    public function test_usuario_nombre_is_required(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload(['usuario_nombre' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['usuario_nombre']);
    }

    public function test_cliente_codigo_is_required(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/salidas', $this->payload(['cliente_codigo' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cliente_codigo']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'usuario_nombre' => 'Juan Perez',
            'cliente_codigo' => '000000',
            'cliente_nombre' => 'CLIENTEPRUBA',
            'referencia' => '',
            'observaciones' => 'Material solicitado',
            'productos' => [
                [
                    'id' => 583,
                    'codigo' => 'Z0001',
                    'nombre' => 'FLETE',
                    'cantidad' => 2,
                    'existencia' => 865,
                ],
            ],
        ], $overrides);
    }
}
