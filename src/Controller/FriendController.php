<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\FriendRepository;
use App\Repository\UserRepository;
use App\Repository\PhotoRepository;
use App\Service\CurrentUserService;

final class FriendController
{
    public function __construct(
        private FriendRepository $friends,
        private UserRepository $users,
        private PhotoRepository $photos,
        private CurrentUserService $currentUser
    ) {}

    public function add(Request $r): Response
    {
        $me = $this->currentUser->get($r);

        $to = (int)$r->post('user_id');
        if ($to <= 0 || $to === $me['id']) {
            return (new Response('', 302))
                ->withHeader('Location', '/profile');
        }

        $this->friends->sendRequest($me['id'], $to);

        return (new Response('', 302))
            ->withHeader('Location', '/profile');
    }

    public function requests(Request $r): Response
    {
        $me = $this->currentUser->get($r);
        $requests = $this->friends->incomingRequests($me['id']);
        $isAuth = true;

        ob_start();
        require ROOT_PATH . '/templates/friends/requests.php';
        return new Response(ob_get_clean());
    }


    public function accept(Request $r): Response
    {
        $this->friends->accept((int)$r->post('request_id'));

        return (new Response('', 302))
            ->withHeader('Location', '/friends/requests');
    }


    public function list(Request $r): Response
    {
        $me = $this->currentUser->get($r);

        $friends = $this->friends->friendsOf($me['id']);
        
        foreach ($friends as &$friend) {
            if (!empty($friend['last_seen_at'])) {
                $lastSeen = new \DateTime($friend['last_seen_at']);
                $now = new \DateTime();

                $friend['is_online'] =
                    ($now->getTimestamp() - $lastSeen->getTimestamp()) <= 300;
            } else {
                $friend['is_online'] = false;
            }
        }

        foreach ($friends as &$f) {
            $f['avatar'] = $this->photos->avatarOf($f['id']);
        }
        unset($f);

        ob_start();
        require ROOT_PATH . '/templates/friends/list.php';
        return new Response(ob_get_clean());
    }

}
