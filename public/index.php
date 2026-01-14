<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Router;
use App\Http\Request;
use App\Http\ControllerFactory;

use App\Security\JwtService;

use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CsrfMiddleware;
use App\Http\Middleware\OptionalAuthMiddleware;
use App\Http\Middleware\HeaderCountersMiddleware;
use App\Http\Middleware\LastSeenMiddleware;

use App\Service\CurrentUserService;

use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\FriendRepository;

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
*/
$authConfig = require ROOT_PATH . '/config/auth.php';

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/
$db = require ROOT_PATH . '/config/database.php';

/*
|--------------------------------------------------------------------------
| REPOSITORIES
|--------------------------------------------------------------------------
*/
$userRepository    = new UserRepository($db);
$messageRepository = new MessageRepository($db);
$friendRepository  = new FriendRepository($db);

/*
|--------------------------------------------------------------------------
| SERVICES
|--------------------------------------------------------------------------
*/
$jwtService = new JwtService($authConfig['jwt']);
$currentUserService = new CurrentUserService($userRepository);

/*
|--------------------------------------------------------------------------
| MIDDLEWARE
|--------------------------------------------------------------------------
*/
$authMiddleware = new AuthMiddleware($jwtService);
$csrfMiddleware = new CsrfMiddleware();

$optionalAuthMiddleware = new OptionalAuthMiddleware($jwtService);

$lastSeenMiddleware = new LastSeenMiddleware(
    $currentUserService,
    $db
);

$headerCountersMiddleware = new HeaderCountersMiddleware(
    $currentUserService,
    $messageRepository,
    $friendRepository
);

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
$request = Request::fromGlobals();
$router  = new Router();

$controllerFactory = new ControllerFactory(
    $db,
    $jwtService
);

$router->setControllerFactory($controllerFactory);

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/
require ROOT_PATH . '/config/routes.php';

/*
|--------------------------------------------------------------------------
| DISPATCH
|--------------------------------------------------------------------------
*/
$response = $router->dispatch($request);

http_response_code($response->getStatus());
foreach ($response->getHeaders() as $k => $v) {
    header("$k: $v");
}

echo $response->getBody();
