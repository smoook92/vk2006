<?php
declare(strict_types=1);

namespace App\Security;

final class SecurityLogger
{
    public static function log(string $message): void
    {
        file_put_contents(
            __DIR__ . '/../../logs/security.log',
            date('[Y-m-d H:i:s] ') . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}
