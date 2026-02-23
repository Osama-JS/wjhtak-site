<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isCustomer()) {
            // Admin users go to admin dashboard
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
