<?php
declare(strict_types=1);

namespace App\Security;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        return $_SESSION['csrf'];
    }

    public static function validate(?string $token): bool
    {
        return isset($_SESSION['csrf'])
            && is_string($token)
            && hash_equals($_SESSION['csrf'], $token);
    }
}
