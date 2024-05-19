<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizedUsers = [
            'spispi' => 'spispi@123',
        ];

        $user = $request->server('PHP_AUTH_USER');
        $pass = $request->server('PHP_AUTH_PW');

        if (! isset($authorizedUsers[$user]) || $authorizedUsers[$user] !== $pass) {
            $headers = ['WWW-Authenticate' => 'Basic'];

            return response('Unauthorized', 401, $headers);
        }

        return $next($request);
    }
}
