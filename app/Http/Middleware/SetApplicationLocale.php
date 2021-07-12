<?php

namespace App\Http\Middleware;

use Locale;
use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SetApplicationLocale
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

        /*$user = Auth::guard('api')->user();
        if($user) { // user logged in
            $locale = $user->preferred_language;      
        } else {*/
            if($request->header('Accept-Language')){
                $locale = $request->header('Accept-Language');
            } else {
                $locale = config('app.fallback_locale');
            }
        //}
        $locale = Locale::getPrimaryLanguage(
            Locale::acceptFromHttp($locale)
        );

        if (in_array($locale, array_keys(config('app.locales')))) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
