<?php

namespace App\Http\Middleware;

use Closure;

class UserInactive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->user() && $request->user()->profile){
            if ($request->user()->status == 'inactive') {
                
                if($request->user()->user_type == 'vendor'){
                    return redirect()->route('home')->with('error', trans('common.vendor_not_active'));
                }
                if($request->user()->user_type == 'individual'){
                    return redirect()->route('home')->with('error', trans('common.vendor_not_active'));
                }

                return $next($request);
            }   
        }
        return $next($request);
    }
}
