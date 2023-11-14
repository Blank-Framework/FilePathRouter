<?php

namespace BlankFramework\FilePathRouter\Exception;

use Exception;

class RoutesPathNotFoundException extends \Exception
{
    public function __construct(?string $path = null)
    {
        if (is_null($path)) {
            $message = 'Routes path could not be found';
        } else {
            $message = sprintf('Routes path %s could not be found', $path);
        }

        parent::__construct($message);
    }
}
