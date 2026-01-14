<?php
declare(strict_types=1);

namespace App\Security;

use RuntimeException;

final class JwtService
{
    public function __construct(
        private array $config
    ) {}

    public function generateAccessToken(int $userId): string
    {
        $payload = [
            'iss' => $this->config['issuer'],
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $this->config['access_ttl'],
        ];

        return $this->encode($payload);
    }

    public function decode(string $jwt): array
    {
        [$header, $payload, $signature] = explode('.', $jwt);

        $valid = hash_hmac(
            'sha256',
            "$header.$payload",
            $this->config['secret'],
            true
        );

        if (!hash_equals($valid, base64_decode($signature))) {
            throw new RuntimeException('Invalid JWT');
        }

        $data = json_decode(base64_decode($payload), true);

        if ($data['exp'] < time()) {
            throw new RuntimeException('JWT expired');
        }

        return $data;
    }

    private function encode(array $payload): string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body   = base64_encode(json_encode($payload));

        $sig = base64_encode(
            hash_hmac('sha256', "$header.$body", $this->config['secret'], true)
        );

        return "$header.$body.$sig";
    }
}
