<?php

namespace Routing;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class Route implements RouteInterface
{
    /** @var string */
    protected $regex;

    /** @var callable */
    protected $callable;

    /** @var array */
    protected $options;

    /**
     * Route constructor.
     * @param string $regex
     * @param callable $callable
     * @param array $options
     */
    public function __construct(string $regex, callable $callable, array $options = [])
    {
        $this->setRegex($regex);
        $this->setCallable($callable);
        $this->setOptions($options);
    }

    /**
     * @return string
     */
    public function getRegex() : string
    {
        return $this->regex;
    }

    /**
     * @param string $regex
     * @return self
     */
    public function setRegex(string $regex) : self
    {
        $regex = preg_replace('{/{2,}}', '/', $regex);
        $regex = trim($regex, '/');

        $this->regex = $regex;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCallable() : callable
    {
        return $this->callable;
    }

    /**
     * @param callable $callable
     * @return self
     */
    public function setCallable(callable $callable) : self
    {
        $this->callable = $callable;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options) : self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption(string $key) : mixed
    {
        return $this->options[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setOption(string $key, mixed $value) : self
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * @param ServerRequest $request
     * @return Response
     */
    public function match(ServerRequest $request)
    {
        // TODO: check uri in Router (and not in route)
        $path = preg_replace('{/{2,}}', '/', $request->getUri()->getPath());
        //$path = $request->getUri()->getQuery(); // TODO:TEST:DEBUG
        $path = trim($path, '/');
        $path = urldecode($path); // TODO: debug temporaire

        // Regex
        if (!preg_match('{^'.$this->regex.'$}i', $path, $attributes))
            return false;

        foreach (array_filter($attributes, 'is_string', ARRAY_FILTER_USE_KEY) as $ak => $av)
            $request = $request->withAttribute($ak, $av);

        // Options
        foreach ($this->getOptions() as $key => $value)
            switch($key)
            {
                case 'method': if (!in_array($request->getMethod(), explode('|', $value))) return false;
            }

        return call_user_func($this->callable, $request, ...array_filter($attributes, 'is_int', ARRAY_FILTER_USE_KEY)); // TODO: delete key 0
    }
}
