<?php

namespace Router\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class ApiMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
    }
}
