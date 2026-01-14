<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Repository\MessageRepository;
use App\Repository\FriendRepository;
use App\Service\CurrentUserService;

final class HeaderCountersMiddleware
{
    public function __construct(
        private CurrentUserService $currentUser,
        private MessageRepository $messages,
        private FriendRepository $friends
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $user = $this->currentUser->getOrNull($request);

        if ($user) {
            $request->setAttribute(
                'unreadMessages',
                $this->messages->unreadCount($user['id'])
            );

            $request->setAttribute(
                'friendRequests',
                $this->friends->incomingCount($user['id'])
            );

            $request->setAttribute('isAuth', true);
        } else {
            $request->setAttribute('isAuth', false);
        }

        return $next($request);
    }
}
