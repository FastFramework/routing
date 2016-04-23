<?php

namespace Routing;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;

class Router
{
    /** @var RouteInterface[] */
    protected $routes;

    /**
     * Router constructor.
     */
    function __construct()
    {
    }

    /**
     * @param string $regex
     * @param callable $callable
     * @param array $options
     * @return RouteInterface
     */
    function map(string $regex, callable $callable, array $options = []) : RouteInterface
    {
        return $this->routes[] = new Route($regex, $callable, $options);
    }

    /**
     * @param ServerRequest $request
     * @return Response
     */
    function match(ServerRequest $request)
    {
        foreach ($this->routes as $route)
            if ($response = $route->match($request))
                return $response;

        return false;
    }

    /**
     * @param ServerRequest $request
     * @return Response
     */
    function matches(ServerRequest $request)
    {
        foreach ($this->routes as $route)
            if ($response = $route->match($request))
                $result = $response;

        return $result ?? false;
    }
}
