<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Service\CurrentUserService;
use PDO;

final class LastSeenMiddleware
{
    public function __construct(
        private CurrentUserService $currentUser,
        private PDO $db
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $user = $this->currentUser->getOrNull($request);

        if ($user) {
            $this->db->prepare(
                'UPDATE users SET last_seen_at = now() WHERE id = :id'
            )->execute(['id' => $user['id']]);
        }

        return $next($request);
    }
}
