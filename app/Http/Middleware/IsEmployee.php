<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsEmployee
{
    /**
     * Gate the employee-facing quiz area. Admins manage quizzes elsewhere,
     * so they are intentionally blocked here. Account must be active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role === 'admin' || ! $user->is_active) {
            abort(403, 'This area is for employees only.');
        }

        return $next($request);
    }
}
