<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow admin and technical_admin to access admin routes
        if ($role === 'admin' && in_array($user->role, ['admin', 'technical_admin'])) {
            return $next($request);
        }

        // Check exact role match for other roles
        if ($user->role === $role) {
            return $next($request);
        }

        // Redirect to appropriate dashboard if role doesn't match
        return $this->redirectToAppropriateDashboard($user);
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    private function redirectToAppropriateDashboard($user): Response
    {
        switch ($user->role) {
            case 'admin':
            case 'technical_admin':
                return redirect()->route('dashboard');
            case 'staff':
                return redirect()->route('dashboard.staff.dashboard');
            default:
                return redirect()->route('login')->with('error', 'Invalid user role');
        }
    }
}