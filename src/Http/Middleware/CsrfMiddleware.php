<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use RuntimeException;

final class CsrfMiddleware
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(16));
        }
    }

    public function handle(Request $request, callable $next): Response
    {
        $token = $request->post('_csrf');

        if (!$token || $token !== $_SESSION['_csrf']) {
            throw new RuntimeException('CSRF validation failed');
        }

        return $next($request);
    }
}
