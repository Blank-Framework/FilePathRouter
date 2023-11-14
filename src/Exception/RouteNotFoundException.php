<?php

namespace BlankFramework\FilePathRouter\Exception;

class RouteNotFoundException extends \Exception
{
    public function __construct(?string $path = null)
    {
        if (is_null($path)) {
            $message = 'Route could not be found for the requested path';
        } else {
            $message = sprintf('Route could not be found for the path %1$s', $path);
        }

        parent::__construct($message);
    }
}
