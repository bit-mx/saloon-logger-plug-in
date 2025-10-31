<?php

use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use BitMx\SaloonLoggerPlugIn\Tests\Assets\Requests\TestGetRequest;
use BitMx\SaloonLoggerPlugIn\Tests\Assets\Requests\TestPostRequest;
use BitMx\SaloonLoggerPlugIn\Tests\Assets\TestJsonConnector;
use BitMx\SaloonLoggerPlugIn\Tests\Assets\TestPlainTextConnector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can log a json post request', function () {

    MockClient::global([
        TestPostRequest::class => MockResponse::make([]),
    ]);

    $connector = new TestJsonConnector;
    $request = new TestPostRequest('1', 'test');
    $connector->send($request);
    /** @var SaloonLogger $trace */
    $trace = $connector->getLogger()->getModel()->trace_id;

    expect(SaloonLogger::count())->toBe(1);
    $log = SaloonLogger::first();
    expect($log->trace_id)->toBe($trace)
        ->and($log->phase)->toBe('response')
        ->and($log->method)->toBe('POST')
        ->and($log->endpoint)->toBe('https://google.com/post')
        ->and($log->headers)->toBe([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Trace-Id' => $trace,
        ])
        ->and($log->query)->toBe([])
        ->and($log->status)->toBe(200)
        ->and($log->response)->not()->toBeNull();

});

it('can log a json get request', function () {

    MockClient::global([
        TestGetRequest::class => MockResponse::make([]),
    ]);

    $connector = new TestJsonConnector;
    $request = new TestGetRequest('1', 'test');
    $connector->send($request);
    $trace = $connector->getLogger()->getModel()->trace_id;

    expect(SaloonLogger::count())->toBe(1);
    $log = SaloonLogger::first();

    expect($log->trace_id)->toBe($trace)
        ->and($log->phase)->toBe('response')
        ->and($log->method)->toBe('GET')
        ->and($log->endpoint)->toBe('https://google.com/get')
        ->and($log->headers)->toBe([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Trace-Id' => $trace,
        ])
        ->and($log->query)->toBe([])
        ->and($log->status)->toBe(200)
        ->and($log->response)->not()->toBeNull();

});

it('can log a post request', function () {

    MockClient::global([
        TestPostRequest::class => MockResponse::make('ok'),
    ]);

    $connector = new TestPlainTextConnector;
    $request = new TestPostRequest('1', 'test');
    $connector->send($request);
    $trace = $connector->getLogger()->getModel()->trace_id;

    expect(SaloonLogger::count())->toBe(1);
    $log = SaloonLogger::first();

    expect($log->trace_id)->toBe($trace)
        ->and($log->phase)->toBe('response')
        ->and($log->method)->toBe('POST')
        ->and($log->endpoint)->toBe('https://google.com/post')
        ->and($log->headers)->toBe([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Trace-Id' => $trace,
        ])
        ->and($log->query)->toBe([])
        ->and($log->status)->toBe(200)
        ->and($log->response)->not()->toBeNull()
        ->and($log->response)->toBe('ok');

});

it('can log a get request', function () {

    MockClient::global([
        TestGetRequest::class => MockResponse::make('ok'),
    ]);

    $connector = new TestPlainTextConnector;
    $request = new TestGetRequest('1', 'test');
    $connector->send($request);
    $trace = $connector->getLogger()->getModel()->trace_id;

    expect(SaloonLogger::count())->toBe(1);
    $log = SaloonLogger::first();

    expect($log->trace_id)->toBe($trace)
        ->and($log->phase)->toBe('response')
        ->and($log->method)->toBe('GET')
        ->and($log->endpoint)->toBe('https://google.com/get')
        ->and($log->headers)->toBe([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Trace-Id' => $trace,
        ])
        ->and($log->query)->toBe([])
        ->and($log->status)->toBe(200)
        ->and($log->response)->not()->toBeNull()
        ->and($log->response)->toBe('ok');

});
