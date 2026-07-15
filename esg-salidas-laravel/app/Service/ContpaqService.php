<?php

namespace App\Service;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ContpaqService
{
    public function productos(): array
    {
        return $this->get('ComercialProductos');
    }

    public function producto(int|string $id): array
    {
        $producto = $this->buscarProducto($id);
        $existencia = $this->existencia($id);

        return [
            'id' => $this->value($producto, ['cidproducto', 'id', 'ID', 'Id', 'cProducto', 'CIDPRODUCTO']),
            'codigo' => $this->value($producto, ['ccodigoproducto', 'codigo', 'Codigo', 'CODIGO', 'CCODIGOPRODUCTO']),
            'nombre' => $this->value($producto, ['cnombreproducto', 'nombre', 'Nombre', 'NOMBRE', 'CNOMBREPRODUCTO']),
            'existencia' => $existencia,
        ];
    }

    public function existencia(int|string $id): int|float|string|null
    {
        $data = $this->request('ComercialExistencia/' . $id);

        if (is_array($data)) {
            return $this->value($data, ['existencia', 'Existencia', 'EXISTENCIA', 'cantidad', 'Cantidad']);
        }

        return $data;
    }

    public function unidadesMedida(): array
    {
        return $this->get('ComercialUnidadMedida');
    }

    public function clientesInternos(): array
    {
        $url = (string) config('services.contpaq.clientes_proveedores_url', '');

        if ($url === '') {
            throw new RuntimeException('El endpoint de clientes/proveedores CONTPAQi no esta configurado.');
        }

        $ttl = (int) config('services.contpaq.clientes_internos_cache_ttl', 300);
        $codigosPermitidos = $this->codigosConfigurados((string) config('services.contpaq.clientes_internos_codigos', ''));

        return Cache::remember('contpaq_clientes_internos_' . md5(implode(',', $codigosPermitidos)), $ttl, function () use ($url, $codigosPermitidos) {
            return collect($this->extractList($this->request($url)))
                ->map(fn (array $cliente) => $this->normalizarClienteInterno($cliente))
                ->filter(fn (?array $cliente) => $cliente !== null)
                ->when($codigosPermitidos !== [], fn ($clientes) => $clientes->filter(
                    fn (array $cliente) => in_array($cliente['codigo'], $codigosPermitidos, true)
                ))
                ->values()
                ->all();
        });
    }

    public function unidadesVehiculares(): array
    {
        $url = (string) config('services.contpaq.unidades_url', '');

        if ($url === '') {
            throw new RuntimeException('El endpoint de unidades vehiculares CONTPAQi no esta configurado.');
        }

        $ttl = (int) config('services.contpaq.unidades_cache_ttl', 300);

        return Cache::remember('contpaq_unidades_vehiculares', $ttl, function () use ($url) {
            return collect($this->extractList($this->request($url)))
                ->map(fn (array $unidad) => $this->normalizarUnidadVehicular($unidad))
                ->filter(fn (?array $unidad) => $unidad !== null)
                ->sortBy(fn (array $unidad) => (int) $unidad['numero'])
                ->values()
                ->all();
        });
    }

    public function registrarDocumentoFactura(array $payload): array
    {
        $url = (string) config('services.contpaq.documentos_facturas_url');

        if ($url === '') {
            throw new RuntimeException('La URL de documentos CONTPAQi no esta configurada.');
        }

        $inicio = microtime(true);

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('services.contpaq.documentos_timeout', 60))
                ->post($url, $payload);

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json() ?? ['body' => $response->body()],
                'error_type' => $response->successful() ? null : 'upstream_http_error',
                'url' => $url,
                'duration_ms' => $this->durationMs($inicio),
            ];
        } catch (ConnectionException $exception) {
            $errorType = $this->isTimeout($exception->getMessage()) ? 'timeout_error' : 'connection_error';

            Log::error('Error al registrar documento en CONTPAQi.', [
                'message' => $exception->getMessage(),
                'error_type' => $errorType,
                'url' => $url,
                'duration_ms' => $this->durationMs($inicio),
            ]);

            return [
                'ok' => false,
                'status' => null,
                'data' => ['message' => $exception->getMessage()],
                'error_type' => $errorType,
                'url' => $url,
                'duration_ms' => $this->durationMs($inicio),
            ];
        }
    }

    private function buscarProducto(int|string $id): array
    {
        foreach ($this->productos() as $producto) {
            $productoId = $this->value($producto, ['cidproducto', 'id', 'ID', 'Id', 'cProducto', 'CIDPRODUCTO']);

            if ((string) $productoId === (string) $id) {
                return $producto;
            }
        }

        throw new RuntimeException('Producto no encontrado en CONTPAQi.');
    }

    private function get(string $endpoint): array
    {
        $data = $this->request($endpoint);

        if (! is_array($data)) {
            throw new RuntimeException('CONTPAQi respondio con un formato invalido.');
        }

        return $this->extractList($data);
    }

    private function request(string $endpoint): mixed
    {
        $baseUrl = rtrim((string) config('services.contpaq.base_url'), '/');

        if ($baseUrl === '') {
            throw new RuntimeException('La URL base de CONTPAQi no esta configurada.');
        }

        $url = str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')
            ? $endpoint
            : $baseUrl . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('services.contpaq.timeout', 15))
                ->retry(
                    (int) config('services.contpaq.retry_times', 2),
                    (int) config('services.contpaq.retry_sleep', 250)
                )
                ->get($url)
                ->throw();

            return $response->json();
        } catch (ConnectionException|RequestException|RuntimeException $exception) {
            Log::error('Error al consultar CONTPAQi.', [
                'endpoint' => $endpoint,
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException(
                'No fue posible consultar la informacion de CONTPAQi.',
                previous: $exception
            );
        }
    }

    private function extractList(array $data): array
    {
        if (array_is_list($data)) {
            return $data;
        }

        foreach (['value', 'data', 'items', 'productos', 'clientes', 'unidades', 'result', 'results'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                return array_is_list($data[$key]) ? $data[$key] : [$data[$key]];
            }
        }

        return [$data];
    }

    private function value(array $data, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
        }

        return null;
    }

    private function normalizarClienteInterno(array $cliente): ?array
    {
        $codigo = $this->value($cliente, [
            'codigoClienteProveedor',
            'CodigoClienteProveedor',
            'CODIGOCLIENTEPROVEEDOR',
            'codigo_cliente_proveedor',
            'codigo',
            'ccodigocliente',
            'CCODIGOCLIENTE',
        ]);
        $nombre = $this->value($cliente, [
            'nombre',
            'nombreCliente',
            'NombreCliente',
            'cnombrecliente',
            'CNOMBRECLIENTE',
            'razonSocial',
            'crazonsocial',
            'CRAZONSOCIAL',
        ]);

        if ($codigo === null || $nombre === null) {
            return null;
        }

        return [
            'id' => $this->value($cliente, ['cidclienteproveedor', 'id', 'ID']),
            'codigo' => (string) $codigo,
            'nombre' => (string) $nombre,
        ];
    }

    private function normalizarUnidadVehicular(array $unidad): ?array
    {
        $numero = $this->value($unidad, ['numeroEconomico', 'numero_economico', 'numero', 'NumeroEconomico']);

        if ($numero === null || (int) $numero <= 0) {
            return null;
        }

        $idUnidad = $this->value($unidad, ['idunidad', 'idUnidad', 'id_unidad', 'id']);
        $tipo = trim((string) ($this->value($unidad, ['tipo', 'Tipo']) ?? ''));
        $marca = trim((string) ($this->value($unidad, ['marca', 'Marca']) ?? ''));
        $modelo = trim((string) ($this->value($unidad, ['modelo', 'Modelo']) ?? ''));
        $placas = trim((string) ($this->value($unidad, ['placas', 'Placas']) ?? ''));

        return [
            // Este id corresponde al numero economico que se envia como movimientos[].unidad.
            'id' => (int) $numero,
            'numero' => (string) $numero,
            'nombre' => trim('Unidad ' . $numero . ($tipo !== '' ? ' - ' . $tipo : '')),
            'contpaq_id' => $idUnidad !== null ? (int) $idUnidad : null,
            'tipo' => $tipo,
            'marca' => $marca,
            'modelo' => $modelo,
            'placas' => $placas,
        ];
    }

    private function codigosConfigurados(string $codigos): array
    {
        if (trim($codigos) === '') {
            return [];
        }

        return collect(explode(',', $codigos))
            ->map(fn (string $codigo) => trim($codigo))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function durationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }

    private function isTimeout(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'timed out')
            || str_contains($message, 'timeout')
            || str_contains($message, 'curl error 28');
    }
}
