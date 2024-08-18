<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Closure;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class CustomThrottleRequests extends ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        // Perform throttle check
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            throw new ThrottleRequestsException(
                'You have exceeded the rate limit.',
                null,
                ['X-RateLimit-Limit' => $maxAttempts, 'X-RateLimit-Remaining' => 0]
            );
        }

        // Allow the request to proceed
        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }
}
