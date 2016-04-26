<?php

namespace Routing;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class Route
{
    /** @var string */
    protected $pattern;

    /** @var callable[] */
    protected $callables;

    /** @var array */
    protected $options = [];

    /**
     * Route constructor.
     * @param string $pattern
     * @param callable $callable
     * @param callable[] ...$callables
     */
    public function __construct(string $pattern, $callable, ...$callables)
    {
        array_unshift($callables, $callable);

        $this->setPattern($pattern);
        $this->setCallables(...$callables);
    }

    /**
     * @return string
     */
    public function getPattern() : string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return self
     */
    public function setPattern(string $pattern) : self
    {
        $pattern = preg_replace('{/{2,}}', '/', $pattern);
        $pattern = trim($pattern, '/');

        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return array
     */
    public function getCallables() : array
    {
        return $this->callables;
    }

    /**
     * @param callable $callable
     * @param callable[] ...$callables
     * @return self
     */
    public function setCallables(callable $callable, callable ...$callables) : self
    {
        array_unshift($callables, $callable);

        $this->callables = $callables;
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
}
