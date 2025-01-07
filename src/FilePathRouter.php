<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter;

use BlankFramework\FilePathRouter\Exception\InvalidRouteException;
use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;
use BlankFramework\RoutingInterfaces\RouteInterface;
use BlankFramework\RoutingInterfaces\SimpleRouterInterface;
use Psr\Http\Message\RequestInterface;

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
    public function routeRequest(RequestInterface $request): RouteInterface
    {
        $path = $request->getUri()->getPath();

        if ($this->isHome($path)) {
            $route = $this->homeRoute();

            if (!file_exists($route)) {
                throw new RouteNotFoundException($path);
            }

            return $this->loadRoute($route);
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


    private function homeRoute(): string
    {
        return $this->makeRoute($this->routesPath);
    }


    /**
     * @throws RouteNotFoundException
     * @throws InvalidRouteException
     */
    private function findRoute(string $path): RouteInterface
    {
        $pathParts = explode('/', trim($path, '/'));
        $routePath = $this->routesPath;

        if (count($pathParts) === 1) {
            $routePath .= sprintf('/%s', $pathParts[0]);

            if ($this->routeExists($routePath)) {
                $route = $this->makeRoute($routePath);
                if (!file_exists($route)) {
                    throw new RouteNotFoundException($path);
                }
                return $this->loadRoute($route);
            }

            throw new RouteNotFoundException($path);
        }

        foreach ($pathParts as $pathPart) {
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

        $route = $this->makeRoute($routePath);
        if (!file_exists($route)) {
            throw new RouteNotFoundException($path);
        }

        return $this->loadRoute($route);
    }


    private function makeRoute(string $dirPath): string
    {
        return sprintf('%s/index.php', $dirPath);
    }


    private function routeExists(string $routePath): bool
    {
        return is_dir($routePath);
    }

    /**
     * @throws InvalidRouteException
     */
    private function loadRoute(string $filePath): RouteInterface
    {
        $route = require($filePath);

        if (!($route instanceof RouteInterface)) {
            throw new InvalidRouteException();
        }

        return $route;
    }
}
