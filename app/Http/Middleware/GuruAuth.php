<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('guru')->check()) {
            return redirect()->route('guru.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
