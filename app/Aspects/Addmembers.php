<?php

namespace App\Aspects;

use AhmadVoid\SimpleAOP\Aspect;
use App\Models\User;
use App\Traits\ResponseTrait;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Addmembers implements Aspect
{
    use ResponseTrait;

    // The constructor can accept parameters for the attribute
    public function __construct()
    {

    }

    public function executeBefore($request, $controller, $method)
    {

    }

    public function executeAfter($request, $controller, $method, $response)
    {

    }

    public function executeException($request, $controller, $method, $exception)
    {

    }
}
