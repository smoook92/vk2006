<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\MessageRepository;
use App\Repository\FriendRepository;
use RuntimeException;

final class MessageService
{
    public function __construct(
        private MessageRepository $messages,
        private FriendRepository $friends
    ) {}

    public function dialogs(int $userId): array
    {
        return $this->messages->getDialogs($userId);
    }

    public function dialog(int $userId, int $otherId): array
    {
        if (!$this->friends->areFriends($userId, $otherId)) {
            throw new RuntimeException('Not friends');
        }

        return $this->messages->getDialog($userId, $otherId);
    }

    public function send(int $from, int $to, string $body): void
    {
        if (!$this->friends->areFriends($from, $to)) {
            throw new RuntimeException('Not friends');
        }

        if (trim($body) === '') {
            throw new RuntimeException('Empty message');
        }

        $this->messages->send($from, $to, $body);
    }
}
