<?php

namespace BitMx\SaloonLoggerPlugIn;

use BitMx\SaloonLoggerPlugIn\Contracts\HasDefaultBody;
use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerContract;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger as SaloonLoggerModel;
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
     * @param  Request&HasDefaultBody  $request
     */
    public function logRequest(Request $request, Connector $connector): void
    {
        $this->model->update([
            'phase' => 'request',
            'method' => $request->getMethod()->value,
            'endpoint' => $connector->resolveBaseUrl().$request->resolveEndpoint(),
            'headers' => self::sanitize($request->headers()->all()),
            'query' => self::sanitize($request->query()->all()),
            'payload' => self::sanitize($request->getDefaultBody()),
        ]);
    }

    public function logResponse(Response $response): void
    {
        $this->model->update([
            'phase' => 'response',
            'status' => $response->status(),
            'response' => self::sanitizeResponse($response),
        ]);
    }

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

    protected static function sanitize(mixed $data): mixed
    {
        $sanitizers = config('saloon-logger.sanitizers', []);

        foreach ($sanitizers as $class) {
            if (! class_exists($class) || ! is_a($class, SanitizerContract::class, true)) {
                continue;
            }

            $data = $class::sanitize($data);
        }

        return $data;
    }

    /**
     * @return array<string,mixed>|string
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
