<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SimpleAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('simple_logged_in')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
