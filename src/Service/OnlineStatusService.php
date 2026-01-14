<?php
declare(strict_types=1);

namespace App\Service;

final class OnlineStatusService
{
    /**
     * Онлайн, если был активен ≤ 5 минут назад
     */
    public function isOnline(?string $lastSeenAt): bool
    {
        if (!$lastSeenAt) {
            return false;
        }

        $lastSeen = new \DateTime($lastSeenAt);
        $now = new \DateTime();

        return ($now->getTimestamp() - $lastSeen->getTimestamp()) <= 300;
    }

    /**
     * Текст в стиле ВК-2006
     */
    public function formatLastSeen(?string $lastSeenAt): string
    {
        if (!$lastSeenAt) {
            return 'давно не заходил(а)';
        }

        $lastSeen = new \DateTime($lastSeenAt);
        $now = new \DateTime();

        if ($this->isOnline($lastSeenAt)) {
            return 'онлайн';
        }

        if ($lastSeen->format('Y-m-d') === $now->format('Y-m-d')) {
            return 'был(а) сегодня в ' . $lastSeen->format('H:i');
        }

        if ($lastSeen->format('Y-m-d') === $now->modify('-1 day')->format('Y-m-d')) {
            return 'был(а) вчера в ' . $lastSeen->format('H:i');
        }

        return 'был(а) ' . $lastSeen->format('d.m.Y H:i');
    }
}
