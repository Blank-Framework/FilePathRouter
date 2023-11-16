<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter\Exception;

use Exception;

final class RouteNotFoundException extends Exception
{
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
