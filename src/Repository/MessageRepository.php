<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class MessageRepository
{
    public function __construct(
        private PDO $db
    ) {}

    /* ========= Диалог 1-к-1 ========= */
    public function dialog(int $a, int $b): array
    {
        $stmt = $this->db->prepare(
            'SELECT sender_id, receiver_id, body, created_at, is_read
            FROM messages
            WHERE (sender_id = :a AND receiver_id = :b)
                OR (sender_id = :b AND receiver_id = :a)
            ORDER BY created_at'
        );

        $stmt->execute([
            'a' => $a,
            'b' => $b,
        ]);

        return $stmt->fetchAll();
    }

    /* ========= Отправка ========= */
    public function send(int $from, int $to, string $body): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (sender_id, receiver_id, body)
             VALUES (:f, :t, :b)'
        );

        $stmt->execute([
            'f' => $from,
            't' => $to,
            'b' => $body,
        ]);
    }

    /* ========= Список диалогов ========= */
    public function dialogs(int $userId): array
    {
        $stmt = $this->db->prepare(
            '
                SELECT DISTINCT ON (other.id)
                    other.id,
                    other.first_name,
                    other.last_name,
                    other.last_seen_at,
                    m.body,
                    m.created_at,
                    EXISTS (
                        SELECT 1 FROM messages m2
                        WHERE m2.sender_id = other.id
                        AND m2.receiver_id = :me
                        AND m2.is_read = false
                    ) AS has_unread
                FROM messages m
                JOIN users other ON other.id =
                    CASE
                        WHEN m.sender_id = :me THEN m.receiver_id
                        ELSE m.sender_id
                    END
                WHERE m.sender_id = :me OR m.receiver_id = :me
                ORDER BY other.id, m.created_at DESC

            '
        );

        $stmt->execute(['me' => $userId]);
        return $stmt->fetchAll();
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM messages
            WHERE receiver_id = :u AND is_read = false'
        );
        $stmt->execute(['u' => $userId]);

        return (int)$stmt->fetchColumn();
    }

    public function markDialogAsRead(int $me, int $other): void
    {
        $stmt = $this->db->prepare(
            'UPDATE messages
            SET is_read = true
            WHERE receiver_id = :me
            AND sender_id = :other
            AND is_read = false'
        );

        $stmt->execute([
            'me'    => $me,
            'other'=> $other,
        ]);
    }

    public function markAsRead(int $me, int $other): void
    {
        $this->db->prepare(
            '
            UPDATE messages
            SET is_read = true
            WHERE receiver_id = :me
            AND sender_id = :other
            AND is_read = false
            '
        )->execute([
            'me' => $me,
            'other' => $other,
        ]);
    }

}