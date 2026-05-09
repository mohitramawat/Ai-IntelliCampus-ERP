<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ForcePasswordChange Middleware
 *
 * If the logged-in user has must_change_password = true,
 * redirect them to the change-password page regardless of
 * what URL they try to access.
 *
 * Excluded routes: change-password itself, logout, assets.
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            auth()->user()->must_change_password &&
            !$request->routeIs('student.password.change', 'student.password.update', 'logout')
        ) {
            return redirect()->route('student.password.change')
                ->with('warning', 'You must change your temporary password before continuing.');
        }

        return $next($request);
    }
}
