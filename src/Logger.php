<?php

namespace Emontano\SaloonLoggerPlugIn;

use Emontano\SaloonLoggerPlugIn\Models\SaloonLogger as SaloonLoggerModel;
use Illuminate\Support\Str;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;

class Logger
{
    public SaloonLoggerModel $model;

    public function __construct()
    {
        $this->model = SaloonLoggerModel::create([
            'trace_id' => (string) Str::ulid(),
            'phase' => 'boot',
            'method' => 'N/A',
            'endpoint' => 'N/A',
            'headers' => [],
            'query' => [],
            'payload' => [],
            'status' => null,
            'response' => null,
        ]);
    }

    /**
     * Registra la fase 'request'.
     */
    public function logRequest(Request $request, Connector $connector): void
    {
        $this->model->update([
            'phase' => 'request',
            'method' => $request->getMethod()->value,
            'endpoint' => $connector->resolveBaseUrl().$request->resolveEndpoint(),
            'headers' => self::sanitizeData($request->headers()->all()),
            'query' => self::sanitizeData($request->query()->all()),
            'payload' => self::sanitizeData($request->getDefaultBody()),
        ]);
    }

    /**
     * Registra la fase 'response'.
     */
    public function logResponse(Response $response): void
    {
        $this->model->update([
            'phase' => 'response',
            'status' => $response->status(),
            'response' => self::sanitizeResponse($response),
        ]);
    }

    /**
     * Registra la fase 'exception'.
     */
    public function logException(FatalRequestException $exception): void
    {
        $this->model->update([
            'phase' => 'exception',
            'response' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);
    }

    /**
     * Aplica la redacción de campos sensibles (e.g., password, api_key).
     */
    protected static function sanitizeData(array $data): array
    {
        $sensitiveFields = config(
            'saloon-logger.redacted_fields',
            ['password', 'secret', 'key', 'token', 'authorization']
        );

        foreach ($data as $key => $value) {

            $lowerKey = Str::lower($key);
            if (is_array($value)) {
                $data[$key] = self::sanitizeData($value);
            } elseif (in_array($lowerKey, $sensitiveFields)) {
                $data[$key] = '***REDACTED***';
            }
        }

        return $data;
    }

    /**
     * Intenta obtener el cuerpo de la respuesta o el mensaje de error.
     */
    protected static function sanitizeResponse(Response $response): array|string
    {
        // Si es una respuesta JSON, devuelve el array. Si no, devuelve el cuerpo como string (limitado).
        try {
            if ($response->json()) {
                return $response->json();
            }
        } catch (\JsonException) {
        }

        // Limita el tamaño del cuerpo de la respuesta para evitar almacenar archivos binarios grandes
        return Str::limit($response->body(), 5000);
    }

    public function getModel(): SaloonLoggerModel
    {
        return $this->model;
    }
}
