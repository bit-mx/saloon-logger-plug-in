# Saloon logger plug-in  
Es un *plug-in* para laravel de la librería [Saloon v3](https://docs.saloon.dev/upgrade/whats-new-in-v3) de Laravel 
que proporciona observabilidad y trazabilidad automáticas 
para todas las interacciones HTTP realizadas con la librería Saloon. 

Registra de forma transparente la solicitud, la respuesta y las excepciones en la 
base de datos, asociando todos los eventos a un único trace_id (ULID).

# Características
- Trazabilidad: Genera un identificador único para enlazar el **request**, 
 al **response** y cualquier excepción.
- Integración Transparente: Se instala simplemente usando traits sobre el 
conector y el request.
- Sanitización de Datos: censura automáticamente campos sensibles 
(como contraseñas o tokens de autenticación) en los payloads y headers 
logueados.
- Almacenamiento Confiable: Utiliza una tabla dedicada para un almacenamiento 
estructurado.
 
# Instalación
#### Requisitos :
```json    
{
  "php": "^8.2",
  "laravel/framework": "^v12.35.0",
  "saloonphp/saloon": "^3.0"
}
```
#### Instalación Composer
Instala el paquete usando Composer:
> composer require bit-mx/saloon-logger-plug-in

#### Publicar y Ejecutar Migraciones
Este comando publicará la migración en tu directorio database/migrations 
> php artisan vendor:publish --tag=saloon-logger-migrations
> 
> php artisan migrate

Publicar Configuración (Opcional)
Puedes publicar el archivo de configuración para personalizar los campos que se censuran 
>  php artisan vendor:publish --tag=saloon-logger-config

Esto creará config/saloon-logger.php.

## Uso
#### Paso 1: Aplicar el Trait 
Para habilitar la trazabilidad, simplemente agregué los triaits 
`HasLogging`(en el conector) y `ProvidesDefaultBody`(en el request) 

### Connector
```php
use BitMx\SaloonLoggerPlugIn\Traits\HasLogging;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class ExampleConnector extends Connector
{
    use HasLogging;

    public function resolveBaseUrl(): string
    {
        return 'https://example.com';
    }
}
```
### Request
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
# Paso 2: Ejecutar la Petición 
Cada vez que se envíe una solicitud a través de este conector, el paquete 
registrará automáticamente los eventos en la tabla 
```php
$connector = new ExampleConnector;
$request = new ExampleRequest($id);
$connector->send($request);
```
> Si ocurriera una excepción HTTP (ej. 500) o de red, se registrarían 'request' y 'exception'.

## Sanitizadores personalizados

Si necesitas una forma personalizada de sanitizar los payloads o headers antes de almacenarlos, implementa la interfaz `SanitizerRequestContract` y registra tu clase en la configuración del paquete dentro del arreglo `sanitizers.request`.

Ejemplo de sanitizador personalizado de request:

```php
namespace App\Sanitizers;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerRequestContract;

class MyRequestSanitizer implements SanitizerRequestContract
{
    public static function sanitize(mixed $data): mixed
    {
        // perform your custom sanitization here
        return $data;
    }
}
```
Si necesitas una forma personalizada de sanitizar el response antes de almacenarlos, implementa la interfaz `SanitizerResponseContract` y registra tu clase en la configuración del paquete dentro del arreglo `sanitizers.response`.

Ejemplo de sanitizador personalizado de response:

```php
namespace App\Sanitizers;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerResponseContract;
use Saloon\Http\Response;

class MyResponseSanitizer implements SanitizerResponseContract
{
    public static function sanitize(Response $data): mixed
    {
        // perform your custom sanitization here
        return $data;
    }
}
```



Regístralo en `config/saloon-logger.php`:

```php
'sanitizers' => [
        'request' => [
            \BitMx\SaloonLoggerPlugIn\Sanitizers\Request\JsonSanitizerRequest::class,
            \App\Sanitizers\MyRequestSanitizer::class,
        ],
        'response' => [
            \BitMx\SaloonLoggerPlugIn\Sanitizers\Response\JsonSanitizerResponse::class,
            \App\Sanitizers\MyResponseSanitizer::class,
        ],
    ],
```

Los sanitizadores se aplican en el orden en que aparecen en la configuración. Cada sanitizador recibe los datos y debe devolver el valor transformado.

# Prune & Backup

Puedes configurar el paquete para eliminar registros antiguos 
y hacer una copia de seguridad de los registros, 
las opciones están disponibles por separado en el archivo de configuración

