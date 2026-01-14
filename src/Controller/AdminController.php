<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\CurrentUserService;

final class AdminController
{
    public function __construct(
        private CurrentUserService $currentUser,
        private array $config
    ) {}

    public function errors(Request $r): Response
    {
        $me = $this->currentUser->get($r);

        if ($me['id'] !== $this->config['admin_user_id']) {
            return new Response('Forbidden', 403);
        }

        $log = ROOT_PATH . '/var/error.log';
        $content = file_exists($log) ? file_get_contents($log) : '';

        ob_start();
        require ROOT_PATH . '/templates/admin/errors.php';
        return new Response(ob_get_clean());
    }
}
