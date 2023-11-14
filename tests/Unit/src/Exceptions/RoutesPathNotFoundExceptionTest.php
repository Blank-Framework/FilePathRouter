<?php

use BlankFramework\FilePathRouter\Exception\RoutesPathNotFoundException;

it('throws exception with default message', function () {
    throw new RoutesPathNotFoundException();
})->throws(RoutesPathNotFoundException::class, 'Routes path could not be found');

it('throws exception with the message including the path', function () {
    throw new RoutesPathNotFoundException('/routes');
})->throws(RoutesPathNotFoundException::class, 'Routes path /routes could not be found');
