<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Security\JwtService;

final class OptionalAuthMiddleware
{
    public function __construct(
        private JwtService $jwt
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $token = $_COOKIE['access_token'] ?? null;

        if ($token) {
            try {
                $payload = $this->jwt->decode($token);
                $request->setAttribute('auth_user_id', (int)$payload['sub']);
            } catch (\Throwable $e) {
                // silently ignore
            }
        }

        return $next($request);
    }
}
