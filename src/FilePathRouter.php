<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter;

use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;
use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;

final class FilePathRouter
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
     * Routes the request to the appropriate file based on the given path.
     *
     * @param string $path The request path.
     *
     * @return string The path to the route file.
     *
     * @throws RouteNotFoundException If the route file corresponding to the path is not found.
     */
    public function routeRequest(string $path): string
    {
        if ($this->isHome($path)) {
            $route = $this->homeRoute();

            if (!file_exists($route)) {
                throw new RouteNotFoundException($path);
            }

            return $route;
        }

        return $this->findRoute($path);
    }


    /**
     * Sets the base path for the routes.
     *
     * @param string $routesPath The base path for the routes.
     *
     * @throws RoutesPathNotFoundException If the routes path does not exist.
     */
    private function setRoutesPath(string $routesPath): void
    {
        if (!$this->routeExists($routesPath)) {
            throw new RoutesPathNotFoundException($routesPath);
        }

        $this->routesPath = rtrim($routesPath, '/');
    }


    /**
     * Determines if the given path is the home route.
     *
     * @param string $path The path to check.
     *
     * @return bool True if the path is the home route, false otherwise.
     */
    private function isHome(string $path): bool
    {
        return $path === '/' || $path === '';
    }


    /**
     * Gets the route for the home page.
     *
     * @return string The path to the home route.
     */
    private function homeRoute(): string
    {
        return $this->makeRoute($this->routesPath);
    }


    /**
     * Finds and returns the route for a given path.
     *
     * @param string $path The path for which to find the route.
     *
     * @return string The path to the found route.
     *
     * @throws RouteNotFoundException If no route is found for the given path.
     */
    private function findRoute(string $path): string
    {
        $pathParts = explode('/', trim($path, '/'));
        $routePath = $this->routesPath;

        if (count($pathParts) === 1) {
            $routePath .= sprintf('/%s', $pathParts[0]);

            if ($this->routeExists($routePath)) {
                return $this->makeRoute($routePath);
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

        return $this->makeRoute($routePath);
    }


    /**
     * Constructs the full path to a route file in a given directory.
     *
     * @param string $dirPath The directory path.
     *
     * @return string The full path to the route file.
     */
    private function makeRoute(string $dirPath): string
    {
        return sprintf('%s/index.php', $dirPath);
    }


    /**
     * Checks whether a route exists for the given route path.
     *
     * @param string $routePath The path to the route.
     *
     * @return bool True if the route exists, false otherwise.
     */
    private function routeExists(string $routePath): bool
    {
        return is_dir($routePath);
    }
}
