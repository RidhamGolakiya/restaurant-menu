<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRestaurantActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->restaurant && !$user->restaurant->is_active) {
            auth()->logout();
            
            // Optional: Flash a message to the session for the login page to display
            // This might differ depending on how Filament handles feedback on login redirect, 
            // but usually a simple redirect with error bag or notification is tricky without a session persistence.
            // For now, simple redirect. The Login page override will handle the initial blocking check.
            // This middleware acts as a safety net for active sessions.
            
            return redirect()->route('filament.restaurant.auth.login')->withErrors(['email' => 'Your restaurant is currently offline. Please contact administrator.']);
        }

        return $next($request);
    }
}
