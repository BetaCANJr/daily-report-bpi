<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XSSProtection
{
    /**
     * Handle incoming request dan sanitize input
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value) {
            if (!is_null($value)) {
                // Sanitize input: remove dangerous tags dan escape special characters
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });

        $request->merge($input);
        return $next($request);
    }
}