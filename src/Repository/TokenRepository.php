<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class TokenRepository
{
    public function __construct(
        private PDO $db
    ) {}

    public function create(int $userId, string $token, \DateTime $expires): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO refresh_tokens (user_id, token, expires_at)
             VALUES (:u, :t, :e)'
        );

        $stmt->execute([
            'u' => $userId,
            't' => $token,
            'e' => $expires->format('Y-m-d H:i:s'),
        ]);
    }

    public function findValid(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM refresh_tokens
             WHERE token = :t AND revoked = false AND expires_at > now()'
        );
        $stmt->execute(['t' => $token]);

        return $stmt->fetch() ?: null;
    }

    public function revoke(string $token): void
    {
        $stmt = $this->db->prepare(
            'UPDATE refresh_tokens SET revoked = true WHERE token = :t'
        );
        $stmt->execute(['t' => $token]);
    }
}
