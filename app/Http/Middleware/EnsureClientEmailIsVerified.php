<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureClientEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('client')->user();
        if (!$user || !$user->email_verified_at) {
            return redirect()->route('client.verify.show')->withErrors(['verify' => 'Please verify your email to continue.']);
        }
        return $next($request);
    }
}
