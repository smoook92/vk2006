<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\FriendRepository;
use RuntimeException;

final class ProfileService
{
    public function __construct(
        private UserRepository $users,
        private FriendRepository $friends
    ) {}

    public function viewProfile(
        int $profileId,
        ?int $viewerId
    ): array {
        $profile = $this->users->findById($profileId);

        if (!$profile) {
            throw new RuntimeException('Profile not found');
        }

        $isOwner  = $viewerId === $profileId;
        $isFriend = $viewerId
            ? $this->friends->areFriends($viewerId, $profileId)
            : false;

        return [
            'profile'   => $profile,
            'is_owner'  => $isOwner,
            'is_friend' => $isFriend,
        ];
    }
}
