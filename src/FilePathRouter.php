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
     * @return string The absolute path to the index.php file that you should call in your application.
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


    private function makeRoute(string $dirPath): string
    {
        return sprintf('%s/index.php', $dirPath);
    }


    private function routeExists(string $routePath): bool
    {
        return is_dir($routePath);
    }
}
