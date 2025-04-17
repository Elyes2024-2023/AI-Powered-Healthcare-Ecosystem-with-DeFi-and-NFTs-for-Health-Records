<?php
/**
 * Blockchain Rate Limit Middleware
 * 
 * @copyright ELYES 2024-2025
 * @author ELYES
 * @package Healthcare-Ecosystem
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * BlockchainRateLimit
 * 
 * Middleware to limit the rate of blockchain-related requests
 * to prevent abuse and ensure system stability.
 * 
 * @package App\Http\Middleware
 * @author ELYES
 */
class BlockchainRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'blockchain_rate_limit:' . $request->user()->id;
        $limit = 10; // Maximum 10 requests per minute
        $decayMinutes = 1;

        if (Cache::has($key)) {
            $count = Cache::get($key);
            if ($count >= $limit) {
                return response()->json([
                    'message' => 'Too many blockchain requests. Please try again later.'
                ], 429);
            }
            Cache::increment($key);
        } else {
            Cache::put($key, 1, now()->addMinutes($decayMinutes));
        }

        return $next($request);
    }
} 