<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $request->start = microtime(true);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $executionTime = round((microtime(true) - $request->start) * 1000, 2);
        if ($response->exception) {
            Log::channel('daily')->error('Exception :', [
                'user' => Auth::check() ? Auth::user()->id : 'guest',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->all(),
                'exception' => $response->exception->getMessage(),
            ]);
        }else{
            Log::channel('daily')->info('API :', [
                'executionTime' => $executionTime.'ms',
                'user' => Auth::check() ? Auth::user()->id : 'guest',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->all(),
            ]);
        }
    }
}
