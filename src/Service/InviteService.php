<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\InviteRepository;
use RuntimeException;

final class InviteService
{
    public function __construct(
        private InviteRepository $invites
    ) {}

    public function validate(string $token): array
    {
        $invite = $this->invites->findByToken($token);

        if (!$invite) {
            throw new RuntimeException('Invalid invite');
        }

        if ($invite['used_at'] !== null) {
            throw new RuntimeException('Invite already used');
        }

        if (strtotime($invite['expires_at']) < time()) {
            throw new RuntimeException('Invite expired');
        }

        return $invite;
    }

    public function consume(int $inviteId, int $userId): void
    {
        $this->invites->markUsed($inviteId, $userId);
    }

    // ✅ НОВОЕ: список инвайтов пользователя
    public function listByUser(int $userId): array
    {
        return $this->invites->findByCreator($userId);
    }

    // ✅ НОВОЕ: создание инвайта
    public function create(int $userId): void
    {
        $count = count($this->invites->findByCreator($userId));

        if ($count >= 5) {
            throw new RuntimeException('Invite limit reached');
        }
        
        $token = bin2hex(random_bytes(8)); // 16 символов
        $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 7); // 7 дней

        $this->invites->create($token, $userId, $expiresAt);
    }
}
