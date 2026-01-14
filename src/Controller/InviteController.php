<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\InviteService;
use App\Service\CurrentUserService;

final class InviteController
{
    public function __construct(
        private InviteService $invites,
        private CurrentUserService $currentUser
    ) {}

    public function list(Request $r): Response
    {
        $me = $this->currentUser->get($r);
        $list = $this->invites->listByUser($me['id']);

        ob_start();
        require ROOT_PATH . '/templates/invites/list.php';
        return new Response(ob_get_clean());
    }

    public function create(Request $r): Response
    {
        $me = $this->currentUser->get($r);
        $this->invites->create($me['id']);

        return (new Response('', 302))
            ->withHeader('Location', '/invites');
    }
}
