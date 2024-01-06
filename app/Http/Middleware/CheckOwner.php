<?php

namespace App\Http\Middleware;

use App\Models\File;
use App\Models\Group;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class CheckOwner
{
    use ResponseTrait;
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
        $file = File::find($request->id);
        $grope = Group::find($file->group_id);
        if ($grope->user_id!=$user->id){
            return response()->json([
                'success' => false,
                'errNum' => "P01",
                'message' => "You Do Not Have Permission.."
            ],status: 403);
        }
        return $next($request);
    }
}
