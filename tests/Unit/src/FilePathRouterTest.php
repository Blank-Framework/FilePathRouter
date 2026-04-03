<?php

use BlankFramework\FilePathRouter\Exception\InvalidRouteException;
use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;
use BlankFramework\FilePathRouter\FilePathRouter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Server\RequestHandlerInterface;

it('can find routes directory', function () {
    $routesPath = __DIR__ . '/../../../routes';

    new FilePathRouter($routesPath);
})->throwsNoExceptions();

it('throws exception with invalid routes directory', function () {
    $routesPath = __DIR__ . '/not-found';

    new FilePathRouter($routesPath);
})->throws(RoutesPathNotFoundException::class, sprintf('Routes path %s could not be found', __DIR__ . '/not-found'));

it('strips all right slashes if more than one is accidentally added', function () {
    $routesPath = __DIR__ . '/../../../routes///';

    new FilePathRouter($routesPath);
})->throwsNoExceptions();

it('can find home route', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RequestHandlerInterface::class);
});

it('can find path with one segment', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/blog');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RequestHandlerInterface::class);
});

it('throws exception when route is not found', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/not-found');

    $route = $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /not-found');

it('finds routes with dynamic parameters', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/blog/1234');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RequestHandlerInterface::class);
});

it('finds the same route with a different dynamic parameter', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/blog/my-best-post');

    $route = $filePathRouter->routeRequest($request);

    expect($route)->toBeInstanceOf(RequestHandlerInterface::class);
});

it('throws an RouteNotFoundException when the directory has no index.php', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/empty');

    $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /empty');

it('throws an RouteNotFoundException when the directory more than 1 level has no index.php', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/empty/another-empty');

    $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /empty');

it('throws exception when it cannot find second+ segment', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/blog/my-best-post/not-found');

    $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /blog/my-best-post/not-found');

it('throws invalid route when it finds the file but does not return a route interface', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/invalid');

    $filePathRouter->routeRequest($request);
})->throws(InvalidRouteException::class);

it('throw RouteNotFoundException when directory traversal is attempted', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);
    $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.com/../public');


    $filePathRouter->routeRequest($request);
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /../public');
