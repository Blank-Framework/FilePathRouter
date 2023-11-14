<?php

use BlankFramework\FilePathRouter\Exception\RouteNotFoundException;

it('uses the correct default message', function () {
    throw new RouteNotFoundException();
})->throws(RouteNotFoundException::class, 'Route could not be found for the requested path');

it('uses the path in the message if provided', function () {
    throw new RouteNotFoundException('/home');
})->throws(RouteNotFoundException::class, 'Route could not be found for the path /home');
