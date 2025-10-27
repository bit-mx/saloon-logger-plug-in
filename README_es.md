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
> composer require emontan0/saloon-logger-plug-in

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
`HasLogging`(en el conector) y `RequestHelper`(en el request) 

### Connector
```php
use Emontano\SaloonLoggerPlugIn\Traits\HasLogging;
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
# Paso 2: Ejecutar la Petición 
Cada vez que se envíe una solicitud a través de este conector, el paquete 
registrará automáticamente los eventos en la tabla 
```php
$connector = new ExampleConnector;
$request = new ExampleRequest($id);
$connector->send($request);
```
> Si ocurriera una excepción HTTP (ej. 500) o de red, se registrarían 'request' y 'exception'.

