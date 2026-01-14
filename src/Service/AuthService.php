<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\TokenRepository;
use App\Security\PasswordHasher;
use App\Security\JwtService;
use RuntimeException;

final class AuthService
{
    public function __construct(
        private UserRepository $users,
        private TokenRepository $tokens,
        private PasswordHasher $hasher,
        private JwtService $jwt,
        private InviteService $invites
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !$this->hasher->verify($password, $user['password_hash'])) {
            throw new RuntimeException('Invalid credentials');
        }


        return $this->issueTokens((int)$user['id']);
    }

    public function register(array $data): void
    {
        $invite = $this->invites->validate($data['invite']);

        $passwordHash = $this->hasher->hash($data['password']);

        $userId = $this->users->create([
            'email'      => $data['email'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'password'   => $passwordHash, // ← ключ совпадает с repository
        ]);


        $this->invites->consume($invite['id'], $userId);
    }


    private function issueTokens(int $userId): array
    {
        $access  = $this->jwt->generateAccessToken($userId);
        $refresh = bin2hex(random_bytes(32));

        $this->tokens->create(
            $userId,
            $refresh,
            new \DateTime('+14 days')
        );

        return [
            'access'  => $access,
            'refresh' => $refresh,
        ];
    }
}
