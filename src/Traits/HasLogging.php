<?php

namespace BitMx\SaloonLoggerPlugIn\Traits;

use BitMx\SaloonLoggerPlugIn\Logger;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Throwable;

/**
 * @template T of Connector
 *
 * @mixin T
 */
trait HasLogging
{
    private Logger $logger;

    private bool $debug;

    /**
     * Boot the plugin and register the logging hooks.
     *
     * @throws Throwable
     */
    public function bootHasLogging(
        PendingRequest $pendingRequest,
    ): void {
        $this->logger = new Logger;
        $this->debug = config('saloon-logger.debug_mode', false);

        $this->middleware()->onRequest(function (PendingRequest $response) {
            try {
                $connector = $response->getConnector();
                $request = $response->getRequest();
                $this->logger->logRequest($request, $connector);
            } catch (Throwable $e) {
                if ($this->debug) {
                    throw $e;
                }
            }

        });

        $this->middleware()->onResponse(function (Response $response) {

            try {
                $this->logger->logResponse($response);
            } catch (Throwable $e) {
                if ($this->debug) {
                    throw $e;
                }
            }
        });

        $this->middleware()->onFatalException(function (FatalRequestException $exception) {

            try {
                $this->logger->logException($exception);
            } catch (Throwable $e) {
                if ($this->debug) {
                    throw $e;
                }
            }
        });

        if (config('saloon-logger.propagate_header', true)) {
            try {
                $request = $pendingRequest->getRequest();
                $request->headers()->add('X-Trace-Id', $this->logger->getModel()->trace_id);
            } catch (Throwable $e) {
                if ($this->debug) {
                    throw $e;
                }
            }

        }
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }
}
