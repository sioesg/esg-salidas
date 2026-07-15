<?php

namespace App\Services\Salidas;

use App\Exceptions\ContpaqSyncException;
use App\Models\Configuracion;
use App\Models\DetalleFolio;
use App\Models\Folio;
use App\Models\LogSincronizacion;
use App\Models\User;
use App\Service\ContpaqService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalidaService
{
    public function __construct(
        private readonly ContpaqService $contpaqService,
    ) {
    }

    public function registrar(array $data, User $user): Folio
    {
        $folio = DB::transaction(function () use ($data, $user) {
            $folio = Folio::query()->create([
                'folio' => $this->siguienteFolio(),
                'fecha' => now(),
                'usuario_id' => $user->id,
                'usuario_nombre' => $data['usuario_nombre'],
                'departamento_codigo' => $data['cliente_codigo'],
                'departamento_nombre' => $data['cliente_nombre'],
                'cliente_codigo' => $data['cliente_codigo'],
                'cliente_nombre' => $data['cliente_nombre'],
                'unidad_id' => $data['unidad_vehicular'] ?? null,
                'referencia' => $data['referencia'] ?? '',
                'unidad_nombre' => isset($data['unidad_vehicular']) ? (string) $data['unidad_vehicular'] : null,
                'observaciones' => $data['observaciones'] ?? '',
                'total_productos' => count($data['productos']),
                'codigo_almacen' => (string) config('services.contpaq.codigo_almacen', '1'),
                'estado' => 'Pendiente',
            ]);

            foreach ($data['productos'] as $producto) {
                DetalleFolio::query()->create([
                    'folio_id' => $folio->id,
                    'producto_id' => $producto['id'],
                    'codigo_producto' => $producto['codigo'],
                    'nombre_producto' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => 0,
                    'existencia_actual' => $producto['existencia'] ?? null,
                    'observaciones' => $producto['observaciones'] ?? '',
                ]);
            }

            return $folio->load('detalles');
        });

        $payload = $this->construirPayload($folio);

        try {
            $respuesta = $this->contpaqService->registrarDocumentoFactura($payload);
        } catch (RuntimeException $exception) {
            $folio->update([
                'estado' => 'Error',
                'fecha_envio' => now(),
            ]);

            $this->registrarLog($folio, $payload, [
                'ok' => false,
                'status' => null,
                'data' => ['message' => $exception->getMessage()],
                'error_type' => 'internal_error',
            ]);

            throw new ContpaqSyncException(
                'No fue posible sincronizar con CONTPAQi.',
                upstreamMessage: $exception->getMessage(),
                errorType: 'internal_error',
                localFolio: $folio->folio,
            );
        }

        $folio->update([
            'estado' => $respuesta['ok'] ? 'Sincronizado' : 'Error',
            'contpaq_documento_id' => $this->extraerDocumentoId($respuesta['data']),
            'folio_contpaq' => $this->extraerFolioContpaq($respuesta['data']),
            'fecha_envio' => now(),
        ]);

        $this->registrarLog($folio, $payload, $respuesta);

        if (! $respuesta['ok']) {
            throw new ContpaqSyncException(
                'No fue posible sincronizar con CONTPAQi.',
                upstreamStatus: $respuesta['status'],
                upstreamMessage: $this->extraerMensajeRespuesta($respuesta['data']),
                errorType: $respuesta['error_type'] ?? 'upstream_error',
                localFolio: $folio->folio,
            );
        }

        return $folio->fresh(['detalles', 'logsSincronizacion']);
    }

    public function construirPayload(Folio $folio): array
    {
        $folio->loadMissing('detalles');

        return [
            'concepto' => (string) config('services.contpaq.documento_concepto', '102'),
            'serie' => (string) config('services.contpaq.documento_serie', 'CONSUMOS'),
            'codigoClienteProveedor' => $folio->cliente_codigo ?: $folio->departamento_codigo,
            'referencia' => $folio->referencia ?? '',
            'codigoAgente' => (string) config('services.contpaq.codigo_agente', ''),
            'observaciones' => $folio->observaciones ?? '',
            'movimientos' => $folio->detalles
                ->map(fn (DetalleFolio $detalle) => $this->construirMovimiento($folio, $detalle))
                ->values()
                ->all(),
        ];
    }

    private function construirMovimiento(Folio $folio, DetalleFolio $detalle): array
    {
        $movimiento = [
            'codProd' => $detalle->codigo_producto,
            'cantidad' => (float) $detalle->cantidad,
            'precio' => 0.00,
            'codAlmacen' => $folio->codigo_almacen ?: (string) config('services.contpaq.codigo_almacen', '1'),
            'observaciones' => $detalle->observaciones ?? '',
        ];

        if ($folio->unidad_id !== null) {
            $movimiento = ['unidad' => (int) $folio->unidad_id] + $movimiento;
        }

        return $movimiento;
    }

    private function siguienteFolio(): string
    {
        $configuracion = Configuracion::query()
            ->where('activo', true)
            ->lockForUpdate()
            ->first();

        if (! $configuracion) {
            $configuracion = Configuracion::query()->create([
                'folio_prefijo' => '',
                'ultimo_folio' => 0,
                'activo' => true,
            ]);
        }

        $ultimo = max(
            (int) $configuracion->ultimo_folio,
            $this->ultimoFolioNumericoRegistrado(),
        );
        $siguiente = $ultimo + 1;

        $configuracion->update(['ultimo_folio' => $siguiente]);

        return str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);
    }

    private function ultimoFolioNumericoRegistrado(): int
    {
        return Folio::query()
            ->pluck('folio')
            ->filter(fn (?string $folio) => $folio !== null && ctype_digit($folio))
            ->map(fn (string $folio) => (int) $folio)
            ->max() ?? 0;
    }

    private function registrarLog(Folio $folio, array $payload, array $respuesta): void
    {
        LogSincronizacion::query()->create([
            'folio_id' => $folio->id,
            'intento' => $folio->logsSincronizacion()->count() + 1,
            'estatus' => $respuesta['ok'] ? 'Sincronizado' : 'Error',
            'codigo_respuesta' => $respuesta['status'],
            'mensaje' => $respuesta['ok']
                ? 'Documento registrado en CONTPAQi.'
                : ($this->extraerMensajeRespuesta($respuesta['data']) ?: 'No fue posible registrar el documento.'),
            'payload' => $payload,
            'respuesta' => [
                'error_type' => $respuesta['error_type'] ?? null,
                'url' => $respuesta['url'] ?? null,
                'duration_ms' => $respuesta['duration_ms'] ?? null,
                'body' => $respuesta['data'],
            ],
            'fecha_envio' => now(),
        ]);
    }

    private function extraerDocumentoId(mixed $respuesta): ?string
    {
        if (! is_array($respuesta)) {
            return null;
        }

        foreach (['idDocumento', 'id_documento', 'documento_id', 'documentoId', 'id', 'guid'] as $key) {
            if (isset($respuesta[$key])) {
                return (string) $respuesta[$key];
            }
        }

        return null;
    }

    private function extraerFolioContpaq(mixed $respuesta): ?string
    {
        if (! is_array($respuesta)) {
            return null;
        }

        foreach (['folio', 'folioDocumento', 'folio_documento', 'numero', 'numeroDocumento'] as $key) {
            if (isset($respuesta[$key])) {
                return (string) $respuesta[$key];
            }
        }

        return null;
    }

    private function extraerMensajeRespuesta(mixed $respuesta): ?string
    {
        if (is_string($respuesta)) {
            return $respuesta;
        }

        if (! is_array($respuesta)) {
            return null;
        }

        foreach (['message', 'mensaje', 'error', 'Message', 'Mensaje', 'title', 'body'] as $key) {
            if (isset($respuesta[$key])) {
                return is_scalar($respuesta[$key]) ? (string) $respuesta[$key] : json_encode($respuesta[$key]);
            }
        }

        return json_encode($respuesta);
    }
}
