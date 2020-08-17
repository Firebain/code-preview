<?php

namespace App\Http\Middleware;

use Closure;

class HasSubscribtion
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
        if ($request->user()->subscriptions->isEmpty()) {
            abort(403, "Access denied");
        }

        return $next($request);
    }
}
