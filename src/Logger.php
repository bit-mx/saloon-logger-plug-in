<?php

namespace BitMx\SaloonLoggerPlugIn;

use BitMx\SaloonLoggerPlugIn\Contracts\HasDefaultBody;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger as SaloonLoggerModel;
use Illuminate\Support\Str;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;

class Logger
{
    public SaloonLoggerModel $model;

    public Sanitizer $sanitizer;

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
        $this->sanitizer = new Sanitizer;
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
            'headers' => $this->sanitizer->request($request->headers()->all()),
            'query' => $this->sanitizer->request($request->query()->all()),
            'payload' => $this->sanitizer->request($request->getDefaultBody()),
        ]);
    }

    public function logResponse(Response $response): void
    {

        $this->model->update([
            'phase' => 'response',
            'status' => $response->status(),
            'response' => $this->sanitizer->response($response),
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

    public function getModel(): SaloonLoggerModel
    {
        return $this->model;
    }
}
