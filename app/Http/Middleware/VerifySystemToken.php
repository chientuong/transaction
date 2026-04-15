<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Domain\System\Infrastructure\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class VerifySystemToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $systemToken = Setting::get('api_system');
        $providedToken = $request->header('Authorization');

        if (!$providedToken || !str_contains((string)$providedToken, (string)$systemToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid System Token.'
            ], 401);
        }

        return $next($request);
    }
}
