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

> composer require bit-mx/saloon-logger-plug-in

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
To enable traceability, add the `HasLogging` trait to your connector and the `ProvidesDefaultBody` trait to your request.

Connector example:

```php
use BitMx\SaloonLoggerPlugIn\Traits\HasLogging;
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
use BitMx\SaloonLoggerPlugIn\Traits\ProvidesDefaultBody;
use BitMx\SaloonLoggerPlugIn\Contracts\HasDefaultBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class ExampleRequest extends Request  implements HasDefaultBody
{
    use ProvidesDefaultBody;

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

## Custom sanitizers

If you need a custom way to sanitize payloads or headers before they are stored, implement the `SanitizerContract` and register your class in the package configuration under the `sanitizers` array.

Example custom sanitizer:

```php
namespace App\Sanitizers;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerContract;

class MySanitizer implements SanitizerContract
{
    public static function sanitize(mixed $data): mixed
    {
        // perform your custom sanitization here
        return $data;
    }
}
```

Then register it in `config/saloon-logger.php`:

```php
'sanitizers' => [
    \BitMx\SaloonLoggerPlugIn\Sanitizers\JsonSanitizer::class,
    \App\Sanitizers\MySanitizer::class,
],
```

Sanitizers are applied in the order they appear in the configuration. Each sanitizer receives the current payload/headers (mixed) and must return the updated value.

