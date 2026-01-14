<?php
declare(strict_types=1);

use App\Http\Response;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Controller\AuthController;
use App\Controller\ProfileController;
use App\Controller\FriendController;
use App\Controller\MessageController;
use App\Controller\SearchController;
use App\Controller\InviteController;
use App\Controller\PhotoController;
use App\Controller\AdminController;

/*
|--------------------------------------------------------------------------
| Middleware (из index.php)
|--------------------------------------------------------------------------
|
| @var \App\Http\Middleware\AuthMiddleware            $authMiddleware
| @var \App\Http\Middleware\CsrfMiddleware            $csrfMiddleware
| @var \App\Http\Middleware\OptionalAuthMiddleware    $optionalAuthMiddleware
| @var \App\Http\Middleware\LastSeenMiddleware        $lastSeenMiddleware
| @var \App\Http\Middleware\HeaderCountersMiddleware  $headerCountersMiddleware
|
*/

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (без авторизации)
|--------------------------------------------------------------------------
*/

$router->get('/', function () {
    return new Response(
        file_get_contents(ROOT_PATH . '/templates/welcome.php')
    );
});

$router->get('/login',  [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register',  [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

// Моя страница
$router->get(
    '/profile',
    [ProfileController::class, 'me'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

// Редактирование профиля
$router->get(
    '/profile/edit',
    [ProfileController::class, 'editForm'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/profile/edit',
    [ProfileController::class, 'edit'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

// Загрузка аватара
$router->post(
    '/profile/avatar',
    [PhotoController::class, 'uploadAvatar'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

// Выход
$router->get(
    '/logout',
    [AuthController::class, 'logout'],
    [$authMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$router->get(
    '/search',
    [SearchController::class, 'form'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/search',
    [SearchController::class, 'results'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| FRIENDS
|--------------------------------------------------------------------------
*/

$router->get(
    '/friends',
    [FriendController::class, 'list'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->get(
    '/friends/requests',
    [FriendController::class, 'requests'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/friends/add',
    [FriendController::class, 'add'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/friends/accept',
    [FriendController::class, 'accept'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| MESSAGES
|--------------------------------------------------------------------------
*/

$router->get(
    '/messages',
    [MessageController::class, 'dialogs'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->get(
    '/messages/dialog',
    [MessageController::class, 'view'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/messages/send',
    [MessageController::class, 'send'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| PHOTOS
|--------------------------------------------------------------------------
*/

$router->get(
    '/photos',
    [PhotoController::class, 'albums'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/photos/upload',
    [PhotoController::class, 'upload'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| INVITES
|--------------------------------------------------------------------------
*/

$router->get(
    '/invites',
    [InviteController::class, 'list'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

$router->post(
    '/invites/create',
    [InviteController::class, 'create'],
    [$authMiddleware, $csrfMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/errors',
    [AdminController::class, 'errors'],
    [$authMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);

/*
|--------------------------------------------------------------------------
| PUBLIC PROFILES
|--------------------------------------------------------------------------
*/

$router->get(
    '/id{profile_id:\d+}',
    [ProfileController::class, 'view'],
    [$optionalAuthMiddleware, $lastSeenMiddleware, $headerCountersMiddleware]
);
