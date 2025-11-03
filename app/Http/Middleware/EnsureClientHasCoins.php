<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClientHasCoins
{
    public function handle(Request $request, Closure $next)
    {
        $client = auth('client')->user();
        if (!$client || ($client->coins ?? 0) < 1) {
            return response()->json(['status' => 'error', 'message' => 'Not enough coins'], 402);
        }
        return $next($request);
    }
}
