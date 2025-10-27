<?php

namespace Emontano\SaloonLoggerPlugIn\Tests\Assets\Requests;

use Emontano\SaloonLoggerPlugIn\Traits\RequestHelper;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class TestGetRequest extends Request
{
    use RequestHelper;

    public function __construct(
        public string $id,
        public string $name
    ) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/get';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
