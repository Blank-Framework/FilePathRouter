<?php

use BlankFramework\FilePathRouter\Exception\InvalidRouteException;
use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;
use BlankFramework\FilePathRouter\FilePathRouter;
use BlankFramework\RoutingInterfaces\RouteInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

it('Can find routes directory', function () {
    $routesPath = __DIR__ . '/../../../routes';

    new FilePathRouter($routesPath);
})->throwsNoExceptions();

it('can throws exception with invalid routes directory', function () {
    $routesPath = __DIR__ . '/not-found';

    new FilePathRouter($routesPath);
})->throws(RoutesPathNotFoundException::class, sprintf('Routes path %s could not be found', __DIR__ . '/not-found'));

it('Can find home route', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RouteInterface::class);
});

it('Can find path with one segment', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/blog');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RouteInterface::class);
});

it('will throw exception when route is not found', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/not-found');

    $route = $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /not-found');

it('Can find routes with dynamic parameters', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/blog/1234');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RouteInterface::class);
});

it('Can find the same route with a different dynamic parameter', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/blog/my-best-post');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RouteInterface::class);
});

it('will throw exception when it cannot find second+ segment', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/blog/my-best-post/not-found');

    $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /blog/my-best-post/not-found');

it('will throw invalid route when it finds the file but does not return a route interface', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createRequest('GET', 'http://example.com/invalid');

    $filePathRouter->routeRequest($request);
})->throws(InvalidRouteException::class);
