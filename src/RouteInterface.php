<?php

namespace Routing;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;

interface RouteInterface
{
    /**
     * @param ServerRequest $request
     * @return Response
     */
    public function match(ServerRequest $request);
}
