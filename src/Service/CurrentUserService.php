<?php
declare(strict_types=1);

namespace App\Service;

use App\Http\Request;
use App\Repository\UserRepository;
use RuntimeException;

final class CurrentUserService
{
    public function __construct(
        private UserRepository $users
    ) {}

    public function get(Request $request): array
    {
        $userId = $request->getAttribute('auth_user_id');

        if (!$userId) {
            throw new RuntimeException('No current user');
        }

        $user = $this->users->findById((int)$userId);

        if (!$user) {
            throw new RuntimeException('User not found');
        }

        return $user;
    }


    public function getOrNull(Request $r): ?array
    {
        $userId = $r->getAttribute('auth_user_id');
        if (!$userId) {
            return null;
        }

        return $this->users->findById((int)$userId);
    }
}
