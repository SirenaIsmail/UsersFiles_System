<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;

class CheckUpload
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
        $user = auth()->user();
        $group = Group::find($request->group);
        if ($user->id!=$group->user_id){
            return response()->json([
                'success' => false,
                'errNum' => "P01",
                'message' => "You Do Not Have Permission.."
            ],status: 403);
        }
        return $next($request);
    }
}
