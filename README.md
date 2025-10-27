# Saloon Logger Plug-in

A Laravel plug-in for the Saloon HTTP client [Saloon v3](https://docs.saloon.dev/upgrade/whats-new-in-v3) that provides automatic observability and traceability for all HTTP interactions made with the Saloon library.

This package transparently logs requests, responses and exceptions to the database, and links all events using a single trace_id (ULID).

## Features

- Traceability: Generates a unique identifier to tie the request, response and any exception together.
- Transparent integration: Enable logging simply by applying traits to the connector and request classes.
- Data sanitization: Automatically censors sensitive fields (for example passwords or authentication tokens) in logged payloads and headers.
- Reliable storage: Uses a dedicated database table for structured storage of logs.

## Installation

### Requirements
```json
{
  "php": "^8.2",
  "laravel/framework": "^v12.35.0",
  "saloonphp/saloon": "^3.0"
}
```

### Install via Composer
Install the package using Composer:

> composer require emontan0/saloon-logger-plug-in

### Publish and run migrations
This command will publish the migration into your `database/migrations` directory:

> php artisan vendor:publish --tag=saloon-logger-migrations

Then run the migrations:

> php artisan migrate

### Publish configuration (optional)
You can publish the configuration file to customize which fields are censored:

> php artisan vendor:publish --tag=saloon-logger-config

This will create `config/saloon-logger.php`.

## Usage

### Step 1: Apply the Traits
To enable traceability, add the `HasLogging` trait to your connector and the `RequestHelper` trait to your request.

Connector example:

```php
use Emontano\SaloonLoggerPlugIn\Traits\HasLogging;
use Saloon\Http\Connector;

class ExampleConnector extends Connector
{
    use HasLogging;

    public function resolveBaseUrl(): string
    {
        return 'https://example.com';
    }
}
```

Request example:

```php
use Emontano\SaloonLoggerPlugIn\Traits\RequestHelper;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class ExampleRequest extends Request
{
    use RequestHelper;

    public function __construct(
        public string $id,
    ) {}

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/test';
    }

    protected function defaultBody(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
```

### Step 2: Send the Request
Whenever a request is sent through this connector, the package will automatically log the events to the database table.

```php
$connector = new ExampleConnector;
$request = new ExampleRequest($id);
$connector->send($request);
```

If an HTTP or network exception occurs (for example a 500), the package will log the request and the exception.

### Other languages
[Spanish](./README_es.md)

