<?php

namespace App\Http\Middleware;

use Closure;
use http\Header;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $response = $next($request);
        $response->headers->set('Content-Security-Policy', "default-src 'self' ");

        if (!$request->user() || $request->user()->role !== $role) {
            return response()->json(['message' => 'Unauthorized. You do not have the required role.'], 403);
        }

        return $next($request);
    }
}
