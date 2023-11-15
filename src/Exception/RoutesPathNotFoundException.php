<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter\Exception;

use Exception;

/**
 * Exception thrown when a routes path cannot be found.
 */
final class RoutesPathNotFoundException extends Exception
{
    /**
     * Constructs a new RoutesPathNotFoundException.
     *
     * This exception is thrown when the path specified for the routes cannot be found in the system.
     * If a specific path is provided, the exception message includes this path.
     * If no path is given, a generic error message is used.
     *
     * @param null|string $path The path to the routes that could not be found. Optional.
     */
    public function __construct(string $path = null)
    {
        if ($path === null) {
            $message = 'Routes path could not be found';
        } else {
            $message = sprintf(
                'Routes path %s could not be found',
                $path
            );
        }

        parent::__construct($message);
    }
}
