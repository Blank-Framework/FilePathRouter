<?php

namespace BlankFramework\FilePathRouter\Exception;

class InvalidRouteException extends \Exception
{
    public function __construct() {
        parent::__construct("Route must return a RouteInterface");
    }
}
