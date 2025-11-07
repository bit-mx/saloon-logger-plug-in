<?php

namespace BitMx\SaloonLoggerPlugIn\Tests\Assets\Requests;

use BitMx\SaloonLoggerPlugIn\Contracts\HasDefaultBody;
use BitMx\SaloonLoggerPlugIn\Traits\ProvidesDefaultBody;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasStringBody;

class TestPostPlainTextRequest extends Request implements HasBody, HasDefaultBody
{
    use HasStringBody,ProvidesDefaultBody;

    public function __construct(
        public string $id,
        public string $name
    ) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/post';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/plain',
        ];
    }

    protected function defaultBody(): string
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'epoch' => now()->timestamp,
            'password' => 'secret'
        ];

        return 'd='.json_encode($data);
    }
}
