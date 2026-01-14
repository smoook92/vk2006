<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\CurrentUserService;
use App\Service\OnlineStatusService;

final class MessageController
{
    public function __construct(
        private MessageRepository $messages,
        private UserRepository $users,
        private CurrentUserService $currentUser,
        private OnlineStatusService $onlineStatus
    ) {}

    public function dialogs(Request $r): Response
    {
        $me = $this->currentUser->get($r);

        $dialogs = $this->messages->dialogs($me['id']);

        ob_start();
        require ROOT_PATH . '/templates/messages/list.php';
        return new Response(ob_get_clean());
    }


    public function view(Request $r): Response
    {
        $me = $this->currentUser->get($r);
        $otherId = (int)$r->get('user_id');

        if ($otherId <= 0 || $otherId === $me['id']) {
            return new Response('Неверный диалог', 400);
        }

        $other = $this->users->findById($otherId);

        $isOnline = $this->onlineStatus->isOnline($other['last_seen_at']);
        $lastSeenText = $this->onlineStatus->formatLastSeen($other['last_seen_at']);

        $messages = $this->messages->dialog($me['id'], $otherId);

        if (!$other) {
            return new Response('Пользователь не найден', 404);
        }
        
        $this->messages->markAsRead($me['id'], $otherId);

        // $messages = $dialog;
        // 🔥 ВАЖНО: отмечаем как прочитанные
        $this->messages->markDialogAsRead($me['id'], $otherId);

        ob_start();
        require ROOT_PATH . '/templates/messages/dialog.php';
        return new Response(ob_get_clean());
    }

    public function send(Request $r): Response
    {
        $me = $this->currentUser->get($r);

        $to = (int)$r->post('user_id');
        $body = trim((string)$r->post('body'));

        if ($to <= 0 || $body === '') {
            return (new Response('', 302))
                ->withHeader('Location', '/messages');
        }

        $this->messages->send($me['id'], $to, $body);

        return (new Response('', 302))
            ->withHeader('Location', '/messages/dialog?user_id=' . $to);
    }

}
