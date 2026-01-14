<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\CurrentUserService;
use App\Service\OnlineStatusService;
use App\Repository\UserRepository;
use App\Repository\FriendRepository;
use App\Repository\PhotoRepository;
use App\Repository\MessageRepository;

final class ProfileController
{
    public function __construct(
        private CurrentUserService $currentUser,
        private UserRepository $users,
        private FriendRepository $friends,
        private PhotoRepository $photos,
        private MessageRepository $messages,
        private OnlineStatusService $onlineStatus
    ) {}

    /* ========= Моя страница ========= */
    public function me(Request $r): Response
    {
        $user = $this->currentUser->get($r);

        // данные от middleware
        $isAuth         = $r->getAttribute('isAuth') ?? false;
        $unreadMessages = $r->getAttribute('unreadMessages') ?? 0;
        $friendRequests = $r->getAttribute('friendRequests') ?? 0;

        $avatar = $this->photos->avatarOf($user['id']);

        ob_start();
        require ROOT_PATH . '/templates/profile/me.php';
        return new Response(ob_get_clean());
    }

    /* ========= Публичный профиль ========= */
    public function view(Request $r): Response
    {
        $profileId = (int)$r->getAttribute('profile_id');

        $viewer = $this->currentUser->getOrNull($r);
        $isAuth = $viewer !== null;
        $viewerId = $viewer['id'] ?? null;

        $isOwner = $isAuth && $viewerId === $profileId;

        if ($isOwner) {
            return (new Response('', 302))
                ->withHeader('Location', '/profile');
        }

        // ✅ СНАЧАЛА получаем профиль
        $profile = $this->users->findById($profileId);
        if (!$profile) {
            return new Response('Пользователь не найден', 404);
        }

        // ✅ потом онлайн-статус
        $isOnline = $this->onlineStatus->isOnline($profile['last_seen_at']);
        $lastSeenText = $this->onlineStatus->formatLastSeen($profile['last_seen_at']);

        $avatar = $this->photos->avatarOf($profileId);

        $status = 'none';
        if ($isAuth) {
            $status = $this->friends->relationStatus($viewerId, $profileId);
        }

        ob_start();
        require ROOT_PATH . '/templates/profile/view.php';
        return new Response(ob_get_clean());
    }

    /* ========= Форма редактирования ========= */
    public function editForm(Request $r): Response
    {
        $user = $this->currentUser->get($r);

        ob_start();
        require ROOT_PATH . '/templates/profile/edit.php';
        return new Response(ob_get_clean());
    }

    /* ========= Сохранение ========= */
    public function edit(Request $r): Response
    {
        $user = $this->currentUser->get($r);

        $this->users->updateProfile($user['id'], [
            'first_name'       => trim($r->post('first_name')),
            'last_name'        => trim($r->post('last_name')),
            'birth_date'       => $r->post('birth_date') ?: null,
            'city'             => trim($r->post('city')),
            'university'       => trim($r->post('university')),
            'faculty'          => trim($r->post('faculty')),
            'enrollment_year'  => $r->post('enrollment_year') ?: null,
            'about'            => trim($r->post('about')),
            'interests'        => trim($r->post('interests')),
        ]);

        return (new Response('', 302))
            ->withHeader('Location', '/profile');
    }
    
}