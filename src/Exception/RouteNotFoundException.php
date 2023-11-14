<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter\Exception;

use Exception;

/**
 * Exception thrown when a route cannot be found for a specified path.
 */
final class RouteNotFoundException extends Exception
{
    /**
     * Constructs a new RouteNotFoundException.
     *
     * This exception is thrown when a requested route is not found in the routing system.
     * If a specific path is provided, it includes this path in the error message.
     * If no path is given, a generic error message is used.
     *
     * @param null|string $path The path for which a route could not be found. Optional.
     */
    public function __construct(string $path = null)
    {
        if ($path === null) {
            $message = 'Route could not be found for the requested path';
        } else {
            $message = sprintf('Route could not be found for the path %1$s', $path);
        }

        parent::__construct($message);
    }
}
