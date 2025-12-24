<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only update last_seen for authenticated users
        if (auth()->check()) {
            $user = auth()->user();
            
            // Update last_seen if it's been more than 1 minute since last update
            // This prevents excessive database writes
            if (!$user->last_seen || 
                Carbon::parse($user->last_seen)->lt(Carbon::now()->subMinute())) {
                $user->update([
                    'last_seen' => now(),
                    'is_online' => true
                ]);
            }
        }

        return $next($request);
    }
}

