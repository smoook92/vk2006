<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class FriendRepository
{
    public function __construct(
        private PDO $db
    ) {}

    /* ---------- REQUESTS ---------- */

    public function requestExists(int $from, int $to): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM friend_requests
             WHERE from_user_id = :f AND to_user_id = :t'
        );
        $stmt->execute(['f' => $from, 't' => $to]);

        return (bool)$stmt->fetchColumn();
    }

    public function createRequest(int $from, int $to): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO friend_requests (from_user_id, to_user_id)
             VALUES (:f, :t)'
        );
        $stmt->execute(['f' => $from, 't' => $to]);
    }

    public function incomingCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM friend_requests
            WHERE to_user_id = :u'
        );
        $stmt->execute(['u' => $userId]);

        return (int)$stmt->fetchColumn();
    }


    public function findIncomingRequests(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT fr.id, u.id AS user_id, u.first_name, u.last_name
             FROM friend_requests fr
             JOIN users u ON u.id = fr.from_user_id
             WHERE fr.to_user_id = :u
             ORDER BY fr.created_at DESC'
        );
        $stmt->execute(['u' => $userId]);

        return $stmt->fetchAll();
    }

    public function deleteRequest(int $requestId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM friend_requests WHERE id = :id'
        );
        $stmt->execute(['id' => $requestId]);
    }

    /* ---------- FRIENDSHIPS ---------- */

    public function areFriends(int $a, int $b): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM friendships
             WHERE user_id = :a AND friend_id = :b'
        );
        $stmt->execute(['a' => $a, 'b' => $b]);

        return (bool)$stmt->fetchColumn();
    }

    public function createFriendship(int $a, int $b): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO friendships (user_id, friend_id)
             VALUES (:a, :b), (:b, :a)'
        );
        $stmt->execute(['a' => $a, 'b' => $b]);
    }

    public function getFriends(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.first_name, u.last_name
             FROM friendships f
             JOIN users u ON u.id = f.friend_id
             WHERE f.user_id = :u
             ORDER BY u.last_name'
        );
        $stmt->execute(['u' => $userId]);

        return $stmt->fetchAll();
    }

    public function sendRequest(int $from, int $to): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO friend_requests (from_user_id, to_user_id)
             VALUES (:f, :t)
             ON CONFLICT DO NOTHING'
        );

        $stmt->execute([
            'f' => $from,
            't' => $to,
        ]);
    }

    public function incomingRequests(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT fr.id, u.id AS user_id, u.first_name, u.last_name
             FROM friend_requests fr
             JOIN users u ON u.id = fr.from_user_id
             WHERE fr.to_user_id = :id'
        );

        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    public function accept(int $requestId): void
    {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare(
            'SELECT from_user_id, to_user_id
            FROM friend_requests
            WHERE id = :id'
        );
        $stmt->execute(['id' => $requestId]);
        $req = $stmt->fetch();

        if (!$req) {
            $this->db->rollBack();
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO friendships (user_id, friend_id)
            VALUES (:a, :b), (:b, :a)'
        );
        $stmt->execute([
            'a' => $req['from_user_id'],
            'b' => $req['to_user_id'],
        ]);

        $stmt = $this->db->prepare(
            'DELETE FROM friend_requests WHERE id = :id'
        );
        $stmt->execute(['id' => $requestId]);

        $this->db->commit();
    }

    public function friendsOf(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.first_name, u.last_name
            FROM friendships f
            JOIN users u ON u.id = f.friend_id
            WHERE f.user_id = :u
            ORDER BY u.last_name, u.first_name'
        );

        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function relationStatus(int $me, int $other): string
    {
        // друзья?
        $stmt = $this->db->prepare(
            'SELECT 1 FROM friendships
            WHERE user_id = :me AND friend_id = :other'
        );
        $stmt->execute(['me' => $me, 'other' => $other]);
        if ($stmt->fetch()) {
            return 'friends';
        }

        // исходящая заявка
        $stmt = $this->db->prepare(
            'SELECT 1 FROM friend_requests
            WHERE from_user_id = :me AND to_user_id = :other'
        );
        $stmt->execute(['me' => $me, 'other' => $other]);
        if ($stmt->fetch()) {
            return 'outgoing';
        }

        // входящая заявка
        $stmt = $this->db->prepare(
            'SELECT 1 FROM friend_requests
            WHERE from_user_id = :other AND to_user_id = :me'
        );
        $stmt->execute(['me' => $me, 'other' => $other]);
        if ($stmt->fetch()) {
            return 'incoming';
        }

        return 'none';
    }


}
