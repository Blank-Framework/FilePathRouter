<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter;

use BlankFramework\FilePathRouter\Exception\InvalidRouteException;
use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;
use BlankFramework\RoutingInterfaces\RouteInterface;
use BlankFramework\RoutingInterfaces\SimpleRouterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\SimpleCache\CacheInterface;

class FilePathRouter implements SimpleRouterInterface
{
    private string $routesPath;

    /**
     * @throws RoutesPathNotFoundException
     */
    public function __construct(
        string $routesPath,
        private ?CacheInterface $cache = null,
    ) {
        $this->setRoutesPath($routesPath);
    }


    /**
     * @throws RouteNotFoundException
     * @throws InvalidRouteException
     */
    public function routeRequest(RequestInterface $request): RouteInterface
    {
        $path = $request->getUri()->getPath();

        $route = $this->checkCache($path);

        if ($route !== null) {
            return $route;
        }

        if ($this->isHome($path)) {
            $route = $this->homeRoute();

            if (!file_exists($route)) {
                throw new RouteNotFoundException($path);
            }

            $route = $this->loadRoute($route);

            $this->cacheRoute($path, $route);

            return $route;
        }

        $route = $this->findRoute($path);

        $this->cacheRoute($path, $route);
        return $route;
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

    /**
     * Checks whether a cache interface was given.
     * @since 3.0.0
     */
    private function hasCache(): bool {
        return ! $this->cache instanceof CacheInterface;
    }

    /**
     * This method checks the cache using the given file path to see if the route has been accessed before.
     * If it has been accessed before, and is in the cache, then it will return the cached route. Otherwise
     * it will return null.
     *
     * @since 3.0.0
     */
    private function checkCache(string $path): ?RouteInterface {
        //  Cache is optional, so if there is no cache handler, return null
        if (! $this->cache instanceof CacheInterface) {
            return null;
        }
        //  Start with hashing the path which will be our cache key
        $cacheKey = hash('sha3-512', $path);
        //  Lookup if the key exists
        if (!$this->cache->has($cacheKey)) {
            return null;
        }

        return $this->cache->get($cacheKey);
    }

    /**
     * This method does the opposite of checkCache and caches the route. For a predetermined length of time.
     *
     * @since 3.0.0
     */
    private function cacheRoute(string $path, RouteInterface $route): void {
        //  Cache is optional, so if there is no cache handler, return null
        if (! $this->cache instanceof CacheInterface) {
            return;
        }

        $cacheKey = hash('sha3-512', $path);

        $this->cache->set($cacheKey, $route, 3600);
    }
}
