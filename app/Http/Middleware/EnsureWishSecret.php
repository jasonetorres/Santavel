<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWishSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('santa.wish_webhook_secret');
        $provided = (string) $request->bearerToken();

        if ($secret === '' || ! hash_equals($secret, $provided)) {
            return response()->json([
                'message' => 'Invalid wish webhook secret.',
            ], 401);
        }

        return $next($request);
    }
}
