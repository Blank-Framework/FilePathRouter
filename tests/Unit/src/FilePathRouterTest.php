<?php

use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;
use BlankFramework\FilePathRouter\FilePathRouter;

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

    $route = $filePathRouter->routeRequest('/');

    expect($route)->toBe(sprintf('%1$s/%2$s', $routesPath, 'index.php'));
});

it('Can find path with one segment', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);

    $route = $filePathRouter->routeRequest('/blog');

    expect($route)->toBe(sprintf('%1$s/%2$s', $routesPath, 'blog/index.php'));
});

it('will throw exception when route is not found', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);

    $filePathRouter->routeRequest('/not-found');
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /not-found');

it('Can find routes with dynamic parameters', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);

    $route = $filePathRouter->routeRequest('/blog/1234');

    expect($route)->toBe(sprintf('%1$s/%2$s', $routesPath, 'blog/param/index.php'));
});

it('Can find the same route with a different dynamic parameter', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);

    $route = $filePathRouter->routeRequest('/blog/my-best-post');

    expect($route)->toBe(sprintf('%1$s/%2$s', $routesPath, 'blog/param/index.php'));
});

it('will throw exception when it cannot find second+ segment', function () {
    $routesPath = __DIR__ . '/../../../routes';

    $filePathRouter = new FilePathRouter($routesPath);

    $filePathRouter->routeRequest('/blog/my-best-post/not-found');
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /blog/my-best-post/not-found');
