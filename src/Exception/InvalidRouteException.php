<?php

declare(strict_types=1);

namespace BlankFramework\FilePathRouter\Exception;

final class InvalidRouteException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Route must return a RequestHandlerInterface");
    }
}
