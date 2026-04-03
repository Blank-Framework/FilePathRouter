<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter;

use BlankFramework\FilePathRouter\Exception\InvalidRouteException;
use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;
use BlankFramework\RoutingInterfaces\SimpleRouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FilePathRouter implements SimpleRouterInterface
{
    private string $routesPath;

    /**
     * @throws RoutesPathNotFoundException
     */
    public function __construct(string $routesPath)
    {
        $this->setRoutesPath($routesPath);
    }

    /**
     * @throws RouteNotFoundException
     * @throws InvalidRouteException
     */
    public function routeRequest(ServerRequestInterface $request): RequestHandlerInterface
    {
        $path = $request->getUri()->getPath();

        if ($this->isHome($path)) {
            return $this->resolveAndLoad($this->routesPath, $path);
        }

        return $this->findRoute($path);
    }

    /**
     * @throws RoutesPathNotFoundException
     */
    private function setRoutesPath(string $routesPath): void
    {
        if (!$this->routeExists($routesPath)) {
            throw new RoutesPathNotFoundException($routesPath);
        }

        $this->routesPath = rtrim($routesPath, '/');
    }

    private function isHome(string $path): bool
    {
        return $path === '/' || $path === '';
    }

    /**
     * @throws RouteNotFoundException
     * @throws InvalidRouteException
     */
    private function findRoute(string $path): RequestHandlerInterface
    {
        $pathParts = explode('/', trim($path, '/'));
        $routePath = $this->routesPath;

        foreach ($pathParts as $pathPart) {
            if ($pathPart === '..' || $pathPart === '.') {
                throw new RouteNotFoundException($path);
            }

            $tempRoutePath = sprintf('%s/%s', $routePath, $pathPart);

            if ($this->routeExists($tempRoutePath)) {
                $routePath = $tempRoutePath;

                continue;
            }

            $tempRoutePath = sprintf('%s/%s', $routePath, 'param');

            if ($this->routeExists($tempRoutePath)) {
                $routePath = $tempRoutePath;

                continue;
            }

            throw new RouteNotFoundException($path);
        }

        return $this->resolveAndLoad($routePath, $path);
    }

    private function makeRoute(string $dirPath): string
    {
        return sprintf('%s/index.php', $dirPath);
    }

    /**
     * @throws RouteNotFoundException
     * @throws InvalidRouteException
     */
    private function resolveAndLoad(string $routePath, string $originalPath): RequestHandlerInterface
    {
        $route = $this->makeRoute($routePath);
        if (!file_exists($route)) {
            throw new RouteNotFoundException($originalPath);
        }
        return $this->loadRoute($route);
    }

    private function routeExists(string $routePath): bool
    {
        return is_dir($routePath);
    }

    /**
     * @throws InvalidRouteException
     */
    private function loadRoute(string $filePath): RequestHandlerInterface
    {
        $route = require($filePath);

        if (!($route instanceof RequestHandlerInterface)) {
            throw new InvalidRouteException();
        }

        return $route;
    }
}
