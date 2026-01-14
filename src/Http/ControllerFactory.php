<?php
declare(strict_types=1);

namespace App\Http;

use App\Controller\AdminController;
use App\Controller\AuthController;
use App\Controller\ProfileController;
use App\Controller\FriendController;
use App\Controller\MessageController;
use App\Controller\SearchController;
use App\Controller\PhotoController;
use App\Controller\InviteController;

use App\Service\AuthService;
use App\Service\CurrentUserService;
use App\Service\FriendService;
use App\Service\MessageService;
use App\Service\SearchService;
use App\Service\InviteService;
use App\Service\OnlineStatusService;

use App\Repository\UserRepository;
use App\Repository\FriendRepository;
use App\Repository\MessageRepository;
use App\Repository\SearchRepository;
use App\Repository\TokenRepository;
use App\Repository\PhotoRepository;
use App\Repository\InviteRepository;

use App\Security\PasswordHasher;
use App\Security\JwtService;

use PDO;

final class ControllerFactory
{
    public function __construct(
        private PDO $db,
        private JwtService $jwt
    ) {}

    public function make(string $class): object
    {
        return match ($class) {

        AdminController::class =>
            new AdminController(
                new CurrentUserService(
                    new UserRepository($this->db)
                ),
                require ROOT_PATH . '/config/admin.php'
            ),
            
        AuthController::class =>
            new AuthController(
                new AuthService(
                    new UserRepository($this->db),
                    new TokenRepository($this->db),
                    new PasswordHasher(),
                    $this->jwt,
                    new InviteService(
                        new InviteRepository($this->db)
                    )
                )
            ),

        ProfileController::class =>
            new ProfileController(
            new CurrentUserService(new UserRepository($this->db)),
            new UserRepository($this->db),
            new FriendRepository($this->db),
            new PhotoRepository($this->db),
            new MessageRepository($this->db),
            new OnlineStatusService()
        ),

        FriendController::class =>
            new FriendController(
                new FriendRepository($this->db),
                new UserRepository($this->db),
                new PhotoRepository($this->db),
                new CurrentUserService(
                    new UserRepository($this->db)
                )
            ),

        MessageController::class =>
            new MessageController(
                new MessageRepository($this->db),
                new UserRepository($this->db),
                new CurrentUserService(
                    new UserRepository($this->db)
                ),
                new OnlineStatusService() // ✅ ВОТ ЭТОГО НЕ ХВАТАЛО
            ),


        SearchController::class =>
            new SearchController(
                new UserRepository($this->db)
            ),

        PhotoController::class =>
            new PhotoController(
                new PhotoRepository($this->db),
                new CurrentUserService(
                    new UserRepository($this->db)
                )
            ),

        InviteController::class =>
            new InviteController(
                new InviteService(
                    new InviteRepository($this->db)
                ),
                new CurrentUserService(
                    new UserRepository($this->db)
                )
            ),

        default => throw new \RuntimeException("Unknown controller $class"),
        };
    }
}