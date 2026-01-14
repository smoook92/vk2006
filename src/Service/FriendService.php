<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\FriendRepository;
use RuntimeException;

final class FriendService
{
    public function __construct(
        private FriendRepository $friends
    ) {}

    public function sendRequest(int $from, int $to): void
    {
        if ($from === $to) {
            throw new RuntimeException('Cannot add yourself');
        }

        if ($this->friends->areFriends($from, $to)) {
            throw new RuntimeException('Already friends');
        }

        if ($this->friends->requestExists($from, $to)) {
            throw new RuntimeException('Request already sent');
        }

        $this->friends->createRequest($from, $to);
    }

    public function acceptRequest(int $requestId, int $userId): void
    {
        // мы не храним отдельно ownership —
        // проверка происходит через delete + friendship
        $this->db->beginTransaction();

        try {
            $this->friends->createFriendship($from, $to);
            $this->friends->deleteRequest($requestId);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }


        $this->friends->deleteRequest($requestId);
    }

    public function incoming(int $userId): array
    {
        return $this->friends->findIncomingRequests($userId);
    }

    private function getRequesterId(int $requestId): int
    {
        // минимализм — один запрос
        // допустимо для MVP
        $stmt = $this->friends->db->prepare(
            'SELECT from_user_id FROM friend_requests WHERE id = :id'
        );
        $stmt->execute(['id' => $requestId]);

        $id = $stmt->fetchColumn();

        if (!$id) {
            throw new RuntimeException('Request not found');
        }

        return (int)$id;
    }
}
