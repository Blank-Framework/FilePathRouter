<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter\Exception;

use Exception;

final class RoutesPathNotFoundException extends Exception
{
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
