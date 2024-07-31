<?php

use BlankFramework\RoutingInterfaces\RouteInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

return new class () implements RouteInterface {
    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        return (new Psr17Factory())->createResponse();
    }
};
