<?php

namespace App\Http\Middleware;

use Closure;

class CheckUser
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        if(!session()->get('user')){
            session()->flash('message.error', "You need to login first!");
            return redirect()->route('login.index');
        }
        return $next($request);

    }
}
