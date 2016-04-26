<?php

namespace Routing;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router
{
    /** @var Route[] */
    protected $collection = [];

    /** @var callable[] */
    protected $optionHandlers = []; // TODO: CreateSystem OptionHandler

    /** @var callable */
    protected $resolver;

    /**
     * Router constructor.
     * @param null $resolver
     */
    function __construct($resolver = null)
    {
        $this->resolver = $resolver ? $resolver : function($callable)
        {
            return $callable;
        };
    }

    /**
     * @param string $pattern
     * @param callable $callable
     * @param callable[] ...$callables
     * @return Route
     */
    function map(string $pattern, $callable, ...$callables) : Route
    {
        return $this->collection[] = new Route($pattern, $callable, ...$callables);
    }
    
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $path = preg_replace('{/{2,}}', '/', $request->getUri()->getPath());
        $path = trim($path, '/');
        $path = urldecode($path); // TODO: debug temporaire

        foreach ($this->collection as $route)
        {
            // Pattern
            if (!preg_match('{^'.$route->getPattern().'$}i', $path, $attributes))
                continue;

            // Options
            foreach ($route->getOptions() as $key => $value)
                switch($key)
                {
                    case 'method': if (!in_array($request->getMethod(), explode('|', $value))) continue 3;
                }

            // Todo: add number key in attributes ??? See
            // Attributes
            foreach (array_filter($attributes, 'is_string', ARRAY_FILTER_USE_KEY) as $ak => $av)
                $request = $request->withAttribute($ak, $av);
            
            return $next($request, $response, ...$route->getCallables() );
        }

        // Error 404
        return $next($request, $response);
    }

    // @todo: OLD SYSTEM, delete him later

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    function dispatch(Request $request, Response $response)
    {
        $path = preg_replace('{/{2,}}', '/', $request->getUri()->getPath());
        $path = $request->getUri()->getQuery(); // TODO:TEST:DEBUG
        $path = trim($path, '/');
        $path = urldecode($path); // TODO: debug temporaire

        foreach ($this->collection as $route)
        {
            // Pattern
            if (!preg_match('{^'.$route->getPattern().'$}i', $path, $attributes))
                continue;

            // Options
            foreach ($route->getOptions() as $key => $value)
                switch($key)
                {
                    case 'method': if (!in_array($request->getMethod(), explode('|', $value))) continue 3;
                }

            // Attributes
            foreach (array_filter($attributes, 'is_string', ARRAY_FILTER_USE_KEY) as $ak => $av)
                $request = $request->withAttribute($ak, $av);

            //return call_user_func($this->callable, $request, ...array_filter($attributes, 'is_int', ARRAY_FILTER_USE_KEY)); // TODO: delete key 0
            return $this->runner($request, $response, ...$route->getCallables() );
        }

        // Error 404
        return $response;
    }

    public function runner(Request $request, Response $response, ...$callables)
    {
        $runner = function ($request, $response, $callables = null) use (&$runner)
        {
            static $queue = [];

            if ($callables)
                $queue = $callables;

            if ($callable = array_shift($queue))
                return call_user_func($this->resolver($callable), $request, $response, $runner);
            else
                return $response;
        };

        return $runner($request, $response, $callables);
    }

    private function resolver($callable)
    {
        $resolver = $this->resolver;
        return $resolver($callable);
    }
}
