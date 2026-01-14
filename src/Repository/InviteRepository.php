<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class InviteRepository
{
    public function __construct(
        private PDO $db
    ) {}

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM invites WHERE token = :t LIMIT 1'
        );
        $stmt->execute(['t' => $token]);
        return $stmt->fetch() ?: null;
    }

    public function markUsed(int $inviteId, int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE invites
             SET used_at = now(), used_by = :u
             WHERE id = :i'
        );
        $stmt->execute([
            'u' => $userId,
            'i' => $inviteId,
        ]);
    }

    // ✅ список инвайтов, созданных пользователем
    public function findByCreator(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT token, used_at, expires_at
            FROM invites
            WHERE created_by = :u
            ORDER BY id DESC'
        );
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }


    // ✅ создание инвайта
    public function create(string $token, int $userId, string $expiresAt): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO invites (token, created_by, expires_at)
             VALUES (:t, :u, :e)'
        );
        $stmt->execute([
            't' => $token,
            'u' => $userId,
            'e' => $expiresAt,
        ]);
    }
}
