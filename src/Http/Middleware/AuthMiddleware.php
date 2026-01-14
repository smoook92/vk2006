<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Security\JwtService;
use RuntimeException;

final class AuthMiddleware
{
    public function __construct(
        private JwtService $jwt
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $token = $_COOKIE['access_token'] ?? null;

        if (!$token) {
            return (new Response('', 302))
                ->withHeader('Location', '/login');
        }

        try {
            $payload = $this->jwt->decode($token);
        } catch (RuntimeException) {
            return (new Response('', 302))
                ->withHeader('Location', '/login');
        }

        $request->setAttribute('auth_user_id', (int)$payload['sub']);

        return $next($request);
    }
}
