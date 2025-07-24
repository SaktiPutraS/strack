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
        if (session('role') !== 'admin' && session('role') !== 'user') {
            return redirect('/login')->with('error', 'Akses ditolak. Peran tidak valid.');
        }
        return $next($request);
    }
}
