<?php

namespace App\Aspects;

use AhmadVoid\SimpleAOP\Aspect;
use Illuminate\Support\Facades\DB;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Transaction implements Aspect
{

    // The constructor can accept parameters for the attribute
    public function __construct()
    {

    }

    public function executeBefore($request, $controller, $method)
    {
        DB::beginTransaction();
    }

    public function executeAfter($request, $controller, $method, $response)
    {
        DB::commit();
    }

    public function executeException($request, $controller, $method, $exception)
    {
        DB::rollBack();
    }
}
