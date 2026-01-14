<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\AuthService;

final class AuthController
{
    public function __construct(
        private AuthService $auth
    ) {}

    /* ========= GET /login ========= */
    public function loginForm(): Response
    {
        ob_start();
        require ROOT_PATH . '/templates/auth/login.php';
        return new Response(ob_get_clean());
    }

    /* ========= POST /login ========= */
    public function login(Request $r): Response
    {
        try {
            $tokens = $this->auth->login(
                $r->post('email'),
                $r->post('password')
            );
        } catch (\RuntimeException $e) {
            $error = 'Неверный email или пароль';

            ob_start();
            require ROOT_PATH . '/templates/auth/login.php';
            return new Response(ob_get_clean());
        }

        setcookie(
            'access_token',
            $tokens['access'],
            [
                'httponly' => true,
                'secure'   => false,
                'samesite' => 'Lax',
                'path'     => '/',
            ]
        );

        setcookie(
            'refresh_token',
            $tokens['refresh'],
            [
                'httponly' => true,
                'secure'   => false,
                'samesite' => 'Strict',
                'path'     => '/',
            ]
        );

        return (new Response('', 302))
            ->withHeader('Location', '/profile');
    }


    /* ========= GET /register ========= */
    public function registerForm(): Response
    {
        ob_start();
        require ROOT_PATH . '/templates/auth/register.php';
        return new Response(ob_get_clean());
    }

    /* ========= POST /register ========= */
    public function register(Request $r): Response
    {
        $this->auth->register([
            'invite'     => $r->post('invite'),
            'first_name' => $r->post('first_name'),
            'last_name'  => $r->post('last_name'),
            'email'      => $r->post('email'),
            'password'   => $r->post('password'),
        ]);

        return (new Response('', 302))
            ->withHeader('Location', '/login');
    }

        /* ========= Logout ========= */
    public function logout(Request $r): Response
    {
        // access token
        setcookie(
            'access_token',
            '',
            [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => false,
                'samesite' => 'Lax',
            ]
        );

        // refresh token
        setcookie(
            'refresh_token',
            '',
            [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => false,
                'samesite' => 'Strict',
            ]
        );

        return (new Response('', 302))
        ->withHeader('Location', '/login');
    }

}
